<?php

namespace App\Http\Controllers;

use App\Models\ChatbotLog;
use App\Models\Event;
use App\Models\EventCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class ChatbotController extends Controller
{
    public function chat(Request $request)
    {
        $request->validate(['prompt' => 'required|string|max:1000']);

        $apiKey = env('OPENAI_API_KEY');
        if (empty($apiKey)) {
            return response()->json([
                'response' => "Evoria AI belum aktif karena OPENAI_API_KEY belum diatur di file .env."
            ]);
        }

        $prompt = trim($request->prompt);
        $localFilters = $this->extractLocalFilters($prompt);

        $currentDate = now()->format('Y-m-d H:i:s');
        $filterPrompt = "You are an AI assistant helping to build a database query for an event ticketing app called 'Evoria'.
        Extract the user's intent and search criteria from the prompt. Return ONLY a valid JSON object. Do not include markdown or extra text.
        
        Today's Date and Time Context: {$currentDate}
        
        JSON structure:
        {
            \"intent\": \"event_search|general_help|greeting\",
            \"keyword\": \"specific artist, event title, venue, or location keyword only. null if none\",
            \"city\": \"city/area mentioned, for example Jakarta, Pontianak, Tangerang, Jakarta Selatan. null if none\",
            \"max_price\": \"maximum ticket price in Indonesian Rupiah as a number. '200 ribu' means 200000. '2 juta' means 2000000. null if none\",
            \"min_price\": \"minimum ticket price in Rupiah, or null\",
            \"free_only\": \"true if user asks for free/gratis events, otherwise false\",
            \"available_only\": \"true unless user clearly asks for past/unavailable events\",
            \"month\": \"1-12 if a month is mentioned, or null\",
            \"year\": \"year if mentioned or implied by month, otherwise null\",
            \"category\": \"category name if mentioned, such as konser, workshop, olahraga, seni, teknologi, festival. null if none\"
        }
        
        Examples:
        Input: 'tunjukkan event yang dibawah 200 ribu'
        Output: {\"intent\":\"event_search\",\"keyword\":null,\"city\":null,\"max_price\":200000,\"min_price\":null,\"free_only\":false,\"available_only\":true,\"month\":null,\"year\":null,\"category\":null}
        Input: 'cari konser gratis di jakarta bulan september'
        Output: {\"intent\":\"event_search\",\"keyword\":null,\"city\":\"Jakarta\",\"max_price\":0,\"min_price\":null,\"free_only\":true,\"available_only\":true,\"month\":9,\"year\":" . now()->format('Y') . ",\"category\":\"konser\"}
        Input: 'halo'
        Output: {\"intent\":\"greeting\",\"keyword\":null,\"city\":null,\"max_price\":null,\"min_price\":null,\"free_only\":false,\"available_only\":true,\"month\":null,\"year\":null,\"category\":null}
        
        User Prompt: '{$prompt}'";

        try {
            $filterResponse = Http::withToken($apiKey)->post('https://api.openai.com/v1/chat/completions', [
                'model' => env('OPENAI_CHATBOT_FILTER_MODEL', 'gpt-4o-mini'),
                'messages' => [['role' => 'user', 'content' => $filterPrompt]],
                'response_format' => ['type' => 'json_object'],
                'temperature' => 0.1,
            ]);

            if (!$filterResponse->successful()) {
                throw new \Exception('Gagal membaca maksud pertanyaan.');
            }

            $jsonString = $filterResponse->json()['choices'][0]['message']['content'];
            $aiFilters = json_decode(trim($jsonString), true) ?: [];
            $filters = $this->mergeFilters($aiFilters, $localFilters, $prompt);

            if (($filters['intent'] ?? 'event_search') === 'greeting' && str_word_count($prompt) <= 3) {
                $reply = 'Halo! Ada yang bisa saya bantu hari ini? Saya bisa carikan event berdasarkan kota, harga, kategori, tanggal, atau nama artis.';
                $this->logChat($request, $prompt, $reply);

                return response()->json(['response' => $reply]);
            }

            if (($filters['intent'] ?? 'event_search') === 'general_help') {
                return $this->answerGeneralQuestion($request, $prompt, $apiKey);
            }

            $query = Event::with(['category', 'tickets'])
                ->where('status', 'published')
                ->where('start_time', '>=', now()->startOfDay());

            if (!empty($filters['keyword'])) {
                $keyword = $filters['keyword'];
                $query->where(function ($keywordQuery) use ($keyword) {
                    $keywordQuery->where('title', 'like', "%{$keyword}%")
                        ->orWhere('description', 'like', "%{$keyword}%")
                        ->orWhere('location_name', 'like', "%{$keyword}%")
                        ->orWhere('address', 'like', "%{$keyword}%");
                });
            }

            if (!empty($filters['city'])) {
                $city = $filters['city'];
                $query->where(function ($cityQuery) use ($city) {
                    $cityQuery->where('location_name', 'like', "%{$city}%")
                        ->orWhere('address', 'like', "%{$city}%");
                });
            }

            if (!empty($filters['month'])) {
                $query->whereMonth('start_time', $filters['month']);
            }
            if (!empty($filters['year'])) {
                $query->whereYear('start_time', $filters['year']);
            }

            if (!empty($filters['category'])) {
                $query->whereHas('category', function ($q) use ($filters) {
                    $q->where('name', 'like', "%{$filters['category']}%");
                });
            }

            $query->whereHas('tickets', fn ($ticketQuery) => $ticketQuery->where('available_qty', '>', 0));

            if (($filters['free_only'] ?? false) || (isset($filters['max_price']) && (float) $filters['max_price'] === 0.0)) {
                $query->whereHas('tickets', fn ($ticketQuery) => $ticketQuery->where('price', 0)->where('available_qty', '>', 0));
            } elseif (isset($filters['max_price']) && $filters['max_price'] !== null) {
                $maxPrice = (float) $filters['max_price'];
                $query->whereHas('tickets', fn ($ticketQuery) => $ticketQuery->where('price', '<=', $maxPrice)->where('available_qty', '>', 0));
            }

            if (isset($filters['min_price']) && $filters['min_price'] !== null) {
                $minPrice = (float) $filters['min_price'];
                $query->whereHas('tickets', fn ($ticketQuery) => $ticketQuery->where('price', '>=', $minPrice)->where('available_qty', '>', 0));
            }

            $events = $query->latest('start_time')->take(8)->get();
            $fallbackEvents = collect();

            if ($events->isEmpty() && isset($filters['max_price']) && $filters['max_price'] !== null) {
                $fallbackEvents = Event::with(['category', 'tickets'])
                    ->where('status', 'published')
                    ->where('start_time', '>=', now()->startOfDay())
                    ->whereHas('tickets', fn ($ticketQuery) => $ticketQuery->where('available_qty', '>', 0))
                    ->get()
                    ->sortBy(fn ($event) => (float) optional($event->tickets->where('available_qty', '>', 0)->sortBy('price')->first())->price)
                    ->take(5)
                    ->values();
            }

            $eventsPayload = $this->buildEventsPayload($events);
            $fallbackPayload = $this->buildEventsPayload($fallbackEvents);

            $eventBaseUrl = rtrim(URL::to('/event'), '/');
            $systemPrompt = "You are a helpful and friendly customer service assistant for 'Evoria', an event marketplace.
            Answer in Indonesian. Use only the event data given below. Do NOT invent events, prices, dates, links, or availability.
            If exact_matches is empty and fallback_events has items, explain that exact matches are unavailable, then offer the fallback events as closest alternatives.
            If both lists are empty, apologize and suggest changing city, budget, category, or date.
	            
            CRITICAL INSTRUCTIONS:
            1. Mention the lowest available ticket price and availability.
            2. ALWAYS provide clickable links using Markdown: [Event Title]({$eventBaseUrl}/slug)
            3. Keep answers concise: greeting sentence + up to 5 bullet points.
            4. If the user asks 'di bawah 200 ribu', interpret it as max_price 200000 Rupiah.
            5. Never cut off mid-sentence, mid-list, or mid-link.

            Parsed filters: " . json_encode($filters) . "
            exact_matches: " . json_encode($eventsPayload) . "
            fallback_events: " . json_encode($fallbackPayload);

            $finalResponse = Http::withToken($apiKey)->post('https://api.openai.com/v1/chat/completions', [
                'model' => env('OPENAI_CHATBOT_RESPONSE_MODEL', 'gpt-4o-mini'),
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $prompt]
                ],
                'max_tokens' => 700,
                'temperature' => 0.35,
            ]);

            if ($finalResponse->successful()) {
                $reply = $finalResponse->json()['choices'][0]['message']['content'];
                
                $this->logChat($request, $prompt, $reply);

                return response()->json(['response' => $reply]);
            }
            
            return response()->json(['response' => 'Maaf, layanan AI sedang bermasalah. Coba lagi sebentar lagi.']);

        } catch (\Exception $e) {
            return response()->json(['response' => 'Maaf, saya belum bisa menganalisis event saat ini. Detail: ' . $e->getMessage()]);
        }
    }

    private function extractLocalFilters(string $prompt): array
    {
        $lowerPrompt = Str::lower($prompt);
        $isBriefGreeting = preg_match('/^\s*(halo|hai|hello|hi)\s*[!.?]*\s*$/u', $lowerPrompt);
        $filters = [
            'intent' => $isBriefGreeting ? 'greeting' : 'event_search',
            'keyword' => null,
            'city' => null,
            'max_price' => null,
            'min_price' => null,
            'free_only' => Str::contains($lowerPrompt, ['gratis', 'free']),
            'available_only' => true,
            'month' => null,
            'year' => null,
            'category' => null,
        ];

        $price = $this->extractPrice($lowerPrompt);
        if ($price !== null) {
            $filters['max_price'] = $price;
        }
        if ($filters['free_only']) {
            $filters['max_price'] = 0;
        }

        $cities = ['Jakarta Selatan', 'Jakarta Timur', 'Jakarta Barat', 'Jakarta Pusat', 'Jakarta Utara', 'Jakarta', 'Tangerang', 'Pontianak', 'Yogyakarta', 'Sleman', 'Bandung', 'Surabaya', 'Semarang', 'Bekasi', 'Depok', 'Bogor', 'Medan', 'Malang', 'Solo'];
        foreach ($cities as $city) {
            if (Str::contains($lowerPrompt, Str::lower($city))) {
                $filters['city'] = $city;
                break;
            }
        }

        $category = EventCategory::query()
            ->pluck('name')
            ->first(fn ($name) => Str::contains($lowerPrompt, Str::lower($name)));

        if (!$category && Str::contains($lowerPrompt, ['konser', 'music', 'musik'])) {
            $category = 'Konser';
        }
        if ($category) {
            $filters['category'] = $category;
        }

        $months = [
            'januari' => 1, 'februari' => 2, 'maret' => 3, 'april' => 4,
            'mei' => 5, 'juni' => 6, 'juli' => 7, 'agustus' => 8,
            'september' => 9, 'oktober' => 10, 'november' => 11, 'desember' => 12,
        ];

        foreach ($months as $monthName => $monthNumber) {
            if (Str::contains($lowerPrompt, $monthName)) {
                $filters['month'] = $monthNumber;
                $filters['year'] = now()->year;
                break;
            }
        }

        return $filters;
    }

    private function answerGeneralQuestion(Request $request, string $prompt, string $apiKey)
    {
        $systemPrompt = "You are Evoria AI Assistant. Answer in Indonesian, concise and helpful.
        Evoria is an event ticket marketplace. Users can search events by category, city, price, date, artist/event name, open event detail, choose available ticket quantity, then checkout.
        Logged-in attendees can see orders and e-tickets in their ticket dashboard.
        Organizers can register/apply as Event Organizer, wait for admin verification, then create/manage events.
        Admin reviews organizer applications and event submissions.
        For payment, Evoria can use Midtrans when configured; free tickets are marked paid immediately.
        If the question is outside Evoria, politely steer back to event discovery, tickets, payment, organizer registration, or account help.";

        $response = Http::withToken($apiKey)->post('https://api.openai.com/v1/chat/completions', [
            'model' => env('OPENAI_CHATBOT_RESPONSE_MODEL', 'gpt-4o-mini'),
            'messages' => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => $prompt],
            ],
            'max_tokens' => 350,
            'temperature' => 0.35,
        ]);

        if (! $response->successful()) {
            return response()->json(['response' => 'Maaf, saya belum bisa menjawab bantuan umum saat ini. Coba tanyakan tentang event, tiket, pembayaran, atau organizer.']);
        }

        $reply = $response->json()['choices'][0]['message']['content'];
        $this->logChat($request, $prompt, $reply);

        return response()->json(['response' => $reply]);
    }

    private function logChat(Request $request, string $prompt, string $reply): void
    {
        ChatbotLog::create([
            'user_id' => $request->user() ? $request->user()->id : null,
            'prompt' => $prompt,
            'response' => $reply,
        ]);
    }

    private function extractPrice(string $prompt): ?int
    {
        if (! preg_match('/(?:di\s*bawah|dibawah|kurang\s+dari|maks(?:imal)?|max|budget|harga)\s*(?:rp\.?\s*)?(\d+(?:[.,]\d+)?)\s*(ribu|rb|k|juta|jt)?/u', $prompt, $matches)) {
            return null;
        }

        $amount = (float) str_replace(',', '.', $matches[1]);
        $unit = $matches[2] ?? '';

        return match ($unit) {
            'juta', 'jt' => (int) round($amount * 1000000),
            'ribu', 'rb', 'k' => (int) round($amount * 1000),
            default => (int) round($amount),
        };
    }

    private function mergeFilters(array $aiFilters, array $localFilters, string $prompt): array
    {
        $filters = array_merge($localFilters, array_filter($aiFilters, fn ($value) => $value !== null && $value !== ''));

        $localPrice = $localFilters['max_price'];
        if ($localPrice !== null) {
            $filters['max_price'] = $localPrice;
        }

        if (isset($filters['max_price']) && (float) $filters['max_price'] > 0 && (float) $filters['max_price'] < 1000 && Str::contains(Str::lower($prompt), ['ribu', 'rb', ' k'])) {
            $filters['max_price'] = (int) $filters['max_price'] * 1000;
        }

        return $filters;
    }

    private function buildEventsPayload($events): array
    {
        return $events->map(function ($event) {
            $availableTickets = $event->tickets->where('available_qty', '>', 0)->sortBy('price')->values();
            $lowestTicket = $availableTickets->first();

            return [
                'title' => $event->title,
                'slug' => $event->slug,
                'category' => $event->category?->name,
                'date' => optional($event->start_time)->translatedFormat('d M Y H:i'),
                'location_name' => $event->location_name,
                'address' => $event->address,
                'lowest_available_price' => $lowestTicket ? (float) $lowestTicket->price : null,
                'available_ticket_count' => $availableTickets->sum('available_qty'),
                'tickets' => $availableTickets->take(3)->map(fn ($ticket) => [
                    'name' => $ticket->name,
                    'price' => (float) $ticket->price,
                    'available_qty' => (int) $ticket->available_qty,
                ])->values(),
            ];
        })->values()->all();
    }
}
