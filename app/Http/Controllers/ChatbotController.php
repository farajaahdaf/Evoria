<?php

namespace App\Http\Controllers;

use App\Models\ChatbotLog;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ChatbotController extends Controller
{
    public function chat(Request $request)
    {
        $request->validate([
            'prompt' => 'required|string|max:1000',
            'lat'    => 'nullable|numeric|between:-90,90',
            'lng'    => 'nullable|numeric|between:-180,180',
        ]);

        $prompt = trim($request->prompt);
        $userLat = $request->filled('lat') ? (float) $request->lat : null;
        $userLng = $request->filled('lng') ? (float) $request->lng : null;
        $apiKey = config('services.openai.api_key');

        if (empty($apiKey)) {
            return response()->json([
                'response' => 'Evoria AI belum aktif.',
            ]);
        }

        try {
            $aiFilters = $this->extractAiFilters($prompt, $apiKey);
            $filters = $this->normalizeAiFilters($aiFilters, $prompt);
        } catch (\Throwable) {
            return response()->json([
                'response' => 'Maaf, layanan AI sedang bermasalah. Coba lagi sebentar lagi.',
            ], 503);
        }

        if (($filters['intent'] ?? 'event_search') === 'greeting') {
            $reply = 'Halo! Saya bisa bantu carikan event berdasarkan kota, kategori, tanggal, harga, atau menampilkan semua event yang tersedia di Evoria.';
            $this->logChat($request, $prompt, $reply);

            return response()->json([
                'response' => $reply,
                // TEMP CHATBOT DEBUG: hapus debug_ai_filters setelah selesai inspect JSON filter dari OpenAI.
                'debug_ai_filters' => [
                    'raw_from_openai' => $aiFilters,
                    'normalized' => $filters,
                ],
            ]);
        }

        if (($filters['intent'] ?? 'event_search') === 'general_help') {
            return $this->answerGeneralQuestion($request, $prompt, $apiKey, $aiFilters, $filters);
        }

        [$events, $fallbackEvents, $totalMatches] = $this->findEvents($filters, $userLat, $userLng);

        // Jawaban pencarian event dibangun deterministik di PHP (tanpa panggilan
        // OpenAI ke-2) → jauh lebih cepat & tidak mungkin timeout di langkah ini.
        // Data event + jarak sudah lengkap, dan mobile menampilkan kartu event
        // yang bisa di-tap di bawah teks ini.
        $displayEvents = $events->isNotEmpty() ? $events : $fallbackEvents;
        $reply = $this->buildEventSearchReply($filters, $events, $fallbackEvents, $totalMatches);
        $this->logChat($request, $prompt, $reply);

        return response()->json([
            'response' => $reply,
            'events'   => $this->buildEventCards($displayEvents),
        ]);
    }

    /**
     * Susun jawaban pencarian event tanpa LLM: daftar rapi + jarak (jika ada).
     */
    private function buildEventSearchReply(array $filters, Collection $events, Collection $fallbackEvents, int $totalMatches): string
    {
        $nearby = (bool) ($filters['nearby'] ?? false);

        if ($events->isNotEmpty()) {
            $intro = $nearby
                ? "Berikut {$events->count()} event terdekat dari lokasimu (total {$totalMatches} ditemukan):"
                : "Ditemukan {$totalMatches} event yang cocok. Berikut daftarnya:";

            return $intro . "\n\n" . $this->formatEventLines($events, $nearby);
        }

        if ($fallbackEvents->isNotEmpty()) {
            return "Tidak ada event yang persis cocok. Mungkin ini menarik buat kamu:\n\n"
                . $this->formatEventLines($fallbackEvents, false);
        }

        return 'Maaf, belum ada event yang cocok. Coba ubah kota, kategori, bulan, tanggal, atau budget ya.';
    }

    private function formatEventLines(Collection $events, bool $withDistance): string
    {
        $rows = $this->buildEventsPayload($events);

        $lines = [];
        foreach ($rows as $i => $event) {
            $meta = [];
            if (! empty($event['date'])) {
                $meta[] = $event['date'];
            }
            if (! empty($event['location_name'])) {
                $meta[] = $event['location_name'];
            }
            if ($withDistance && isset($event['distance_km']) && $event['distance_km'] !== null) {
                $meta[] = "{$event['distance_km']} km dari lokasimu";
            }

            $price = $event['lowest_price'];
            $meta[] = $price === null
                ? 'Harga -'
                : ((float) $price === 0.0 ? 'Gratis' : 'Mulai Rp ' . number_format((float) $price, 0, ',', '.'));

            if (($event['available_ticket_count'] ?? 0) > 0) {
                $meta[] = "{$event['available_ticket_count']} tiket tersedia";
            }

            $number = $i + 1;
            $lines[] = "{$number}. [{$event['link_text']}]({$event['url']})\n   " . implode(' · ', $meta);
        }

        return implode("\n\n", $lines);
    }

    private function extractAiFilters(string $prompt, string $apiKey): array
    {
        $currentDate = now()->format('Y-m-d H:i:s');
        $filterPrompt = "You are an AI assistant for an event ticketing app called Evoria.
Extract the user's intent and search criteria. Return ONLY a valid JSON object.

Today's date/time: {$currentDate}

JSON schema:
{
  \"intent\": \"event_search|general_help|greeting\",
  \"keyword\": \"artist, event title, venue, or location keyword only; null if none\",
  \"city\": \"city/area mentioned, for example Jakarta, Pontianak, Tangerang, Jakarta Selatan; null if none\",
  \"max_price\": \"maximum ticket price in Indonesian Rupiah as a number; null if none\",
  \"min_price\": \"minimum ticket price in Rupiah; null if none\",
  \"free_only\": \"true if user asks for free/gratis events\",
  \"available_only\": \"true if user asks for available tickets/stok; false for all/list/daftar/semua event\",
  \"date_scope\": \"all|upcoming|past\",
  \"month\": \"1-12 if a month is mentioned; null if none\",
  \"year\": \"year if mentioned or implied by month; null if none\",
  \"category\": \"category name or synonym such as konser, workshop, olahraga, seni, teknologi, festival; null if none\",
  \"list_all\": \"true if user asks to list/show all events or events in a city/category without a narrow keyword\",
  \"nearby\": \"true if the user asks for events near them / closest / by distance, for example 'event terdekat', 'paling dekat', 'di sekitar saya', 'dekat lokasi saya', 'nearest', 'near me'; false otherwise\"
}

Rules:
- 'list semua event', 'daftar event', 'event apa saja', and 'event di Jakarta/Pontianak' are event_search with list_all=true.
- If user asks 'yang ada' or 'semua event', use date_scope=all and available_only=false.
- If user asks 'yang akan datang', 'upcoming', or 'tersedia', use date_scope=upcoming.
- Set nearby=true ONLY when the user clearly wants results based on proximity to their own location (terdekat, sekitar saya, dekat sini, nearest, near me). Do not set city when nearby=true unless the user also names a city.
- Do not use the word 'event' itself as keyword.

Examples:
Input: list semua event yang ada
Output: {\"intent\":\"event_search\",\"keyword\":null,\"city\":null,\"max_price\":null,\"min_price\":null,\"free_only\":false,\"available_only\":false,\"date_scope\":\"all\",\"month\":null,\"year\":null,\"category\":null,\"list_all\":true,\"nearby\":false}
Input: list event yang ada di Pontianak
Output: {\"intent\":\"event_search\",\"keyword\":null,\"city\":\"Pontianak\",\"max_price\":null,\"min_price\":null,\"free_only\":false,\"available_only\":false,\"date_scope\":\"all\",\"month\":null,\"year\":null,\"category\":null,\"list_all\":true,\"nearby\":false}
Input: cari konser gratis di jakarta bulan september
Output: {\"intent\":\"event_search\",\"keyword\":null,\"city\":\"Jakarta\",\"max_price\":0,\"min_price\":null,\"free_only\":true,\"available_only\":true,\"date_scope\":\"upcoming\",\"month\":9,\"year\":" . now()->year . ",\"category\":\"konser\",\"list_all\":false,\"nearby\":false}
Input: event apa yang paling dekat dari lokasi saya sekarang?
Output: {\"intent\":\"event_search\",\"keyword\":null,\"city\":null,\"max_price\":null,\"min_price\":null,\"free_only\":false,\"available_only\":true,\"date_scope\":\"upcoming\",\"month\":null,\"year\":null,\"category\":null,\"list_all\":false,\"nearby\":true}

User prompt: {$prompt}";

        $filterResponse = Http::withToken($apiKey)->post('https://api.openai.com/v1/chat/completions', [
            'model' => config('services.openai.chatbot_filter_model', 'gpt-4o-mini'),
            'messages' => [['role' => 'user', 'content' => $filterPrompt]],
            'response_format' => ['type' => 'json_object'],
            'temperature' => 0.1,
        ]);

        if (! $filterResponse->successful()) {
            throw new \RuntimeException('Gagal membaca maksud pertanyaan.');
        }

        $jsonString = $filterResponse->json()['choices'][0]['message']['content'] ?? '{}';

        return json_decode(trim($jsonString), true) ?: [];
    }

    private function findEvents(array $filters, ?float $userLat = null, ?float $userLng = null): array
    {
        $limit = ($filters['list_all'] ?? false) ? 12 : 8;
        $query = Event::with(['category', 'tickets'])
            ->where('status', 'published');

        $this->applyEventFilters($query, $filters);

        // Mode "terdekat": butuh koordinat user. Ambil kandidat yang punya lat/lng,
        // hitung jarak (Haversine), lalu urutkan dari yang paling dekat.
        $wantsNearby = ($filters['nearby'] ?? false) && $userLat !== null && $userLng !== null;
        if ($wantsNearby) {
            $candidates = (clone $query)
                ->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->get();

            $totalMatches = $candidates->count();

            $events = $candidates
                ->map(function ($event) use ($userLat, $userLng) {
                    $event->distance_km = round(
                        $this->haversineKm($userLat, $userLng, (float) $event->latitude, (float) $event->longitude),
                        1
                    );
                    return $event;
                })
                ->sortBy('distance_km')
                ->take($limit)
                ->values();

            // Fallback: kalau tidak ada event berkoordinat, tawarkan event terdekat secara waktu.
            $fallbackEvents = $events->isEmpty()
                ? (clone $query)->orderBy('start_time')->take(5)->get()
                : collect();

            return [$events, $fallbackEvents, $totalMatches];
        }

        $totalMatches = (clone $query)->count();
        $events = $query
            ->orderBy('start_time')
            ->take($limit)
            ->get();

        $fallbackEvents = collect();

        if ($events->isEmpty() && $this->hasStrictFilters($filters)) {
            $fallbackQuery = Event::with(['category', 'tickets'])
                ->where('status', 'published');

            $looseFilters = array_merge($filters, [
                'keyword' => null,
                'max_price' => null,
                'min_price' => null,
                'free_only' => false,
                'available_only' => false,
            ]);

            $this->applyEventFilters($fallbackQuery, $looseFilters);

            $fallbackEvents = $fallbackQuery
                ->orderBy('start_time')
                ->take(5)
                ->get();
        }

        return [$events, $fallbackEvents, $totalMatches];
    }

    private function applyEventFilters($query, array $filters): void
    {
        if (($filters['date_scope'] ?? 'upcoming') === 'upcoming') {
            $query->where('start_time', '>=', now()->startOfDay());
        } elseif (($filters['date_scope'] ?? 'upcoming') === 'past') {
            $query->where('start_time', '<', now()->startOfDay());
        }

        if (! empty($filters['keyword'])) {
            $keyword = $filters['keyword'];
            $query->where(function ($keywordQuery) use ($keyword) {
                $keywordQuery->where('title', 'like', "%{$keyword}%")
                    ->orWhere('description', 'like', "%{$keyword}%")
                    ->orWhere('location_name', 'like', "%{$keyword}%")
                    ->orWhere('address', 'like', "%{$keyword}%");
            });
        }

        if (! empty($filters['city'])) {
            $city = $filters['city'];
            $query->where(function ($cityQuery) use ($city) {
                $cityQuery->where('location_name', 'like', "%{$city}%")
                    ->orWhere('address', 'like', "%{$city}%");
            });
        }

        if (! empty($filters['month'])) {
            $query->whereMonth('start_time', (int) $filters['month']);
        }

        if (! empty($filters['year'])) {
            $query->whereYear('start_time', (int) $filters['year']);
        }

        if (! empty($filters['category'])) {
            $category = $this->normalizeCategoryName($filters['category']);
            $query->whereHas('category', fn ($q) => $q->where('name', 'like', "%{$category}%"));
        }

        if ($filters['available_only'] ?? true) {
            $query->whereHas('tickets', fn ($ticketQuery) => $ticketQuery->where('available_qty', '>', 0));
        }

        if (($filters['free_only'] ?? false) || (isset($filters['max_price']) && (float) $filters['max_price'] === 0.0)) {
            $query->whereHas('tickets', function ($ticketQuery) use ($filters) {
                $ticketQuery->where('price', 0);
                if ($filters['available_only'] ?? true) {
                    $ticketQuery->where('available_qty', '>', 0);
                }
            });
        } elseif (isset($filters['max_price']) && $filters['max_price'] !== null) {
            $maxPrice = (float) $filters['max_price'];
            $query->whereHas('tickets', function ($ticketQuery) use ($maxPrice, $filters) {
                $ticketQuery->where('price', '<=', $maxPrice);
                if ($filters['available_only'] ?? true) {
                    $ticketQuery->where('available_qty', '>', 0);
                }
            });
        }

        if (isset($filters['min_price']) && $filters['min_price'] !== null) {
            $minPrice = (float) $filters['min_price'];
            $query->whereHas('tickets', function ($ticketQuery) use ($minPrice, $filters) {
                $ticketQuery->where('price', '>=', $minPrice);
                if ($filters['available_only'] ?? true) {
                    $ticketQuery->where('available_qty', '>', 0);
                }
            });
        }
    }

    private function answerGeneralQuestion(Request $request, string $prompt, string $apiKey, array $aiFilters = [], array $filters = [])
    {
        $systemPrompt = "You are Evoria AI Assistant. Answer in Indonesian, concise and helpful.
        Evoria is an event ticket marketplace. Users can search events by category, city, price, date, artist/event name, open event detail, choose available ticket quantity, then checkout.
        Logged-in attendees can see orders and e-tickets in their ticket dashboard.
        Organizers can register/apply as Event Organizer, wait for admin verification, then create/manage events.
        Admin reviews organizer applications and event submissions.
        For payment, Evoria can use Midtrans when configured; free tickets are marked paid immediately.
        If the question is outside Evoria, politely steer back to event discovery, tickets, payment, organizer registration, or account help.";

        $response = Http::withToken($apiKey)->post('https://api.openai.com/v1/chat/completions', [
            'model' => config('services.openai.chatbot_response_model', 'gpt-4o-mini'),
            'messages' => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => $prompt],
            ],
            'max_tokens' => 350,
            'temperature' => 0.35,
        ]);

        if (! $response->successful()) {
            return response()->json([
                'response' => 'Maaf, layanan AI sedang bermasalah. Coba lagi sebentar lagi.',
            ], 503);
        }

        $reply = $response->json()['choices'][0]['message']['content'] ?? null;
        if (! is_string($reply) || trim($reply) === '') {
            return response()->json([
                'response' => 'Maaf, layanan AI sedang bermasalah. Coba lagi sebentar lagi.',
            ], 503);
        }

        $this->logChat($request, $prompt, $reply);

        return response()->json([
            'response' => $reply,
            // TEMP CHATBOT DEBUG: hapus debug_ai_filters setelah selesai inspect JSON filter dari OpenAI.
            'debug_ai_filters' => [
                'raw_from_openai' => $aiFilters,
                'normalized' => $filters,
            ],
        ]);
    }

    private function logChat(Request $request, string $prompt, string $reply): void
    {
        // Route /chatbot kini publik (guest boleh pakai). Kalau ada Bearer token yang
        // valid, tetap identifikasi user lewat guard sanctum agar log ter-atribusi.
        $user = $request->user('sanctum') ?? $request->user();

        ChatbotLog::create([
            'user_id' => $user?->id,
            'prompt' => $prompt,
            'response' => $reply,
        ]);
    }

    private function normalizeAiFilters(array $aiFilters, string $prompt): array
    {
        $filters = array_merge([
            'intent' => 'event_search',
            'keyword' => null,
            'city' => null,
            'max_price' => null,
            'min_price' => null,
            'free_only' => false,
            'available_only' => true,
            'date_scope' => 'upcoming',
            'month' => null,
            'year' => null,
            'category' => null,
            'list_all' => false,
            'nearby' => false,
        ], array_filter($aiFilters, fn ($value) => $value !== null && $value !== ''));

        foreach (['free_only', 'available_only', 'list_all', 'nearby'] as $booleanKey) {
            if (isset($filters[$booleanKey])) {
                $filters[$booleanKey] = filter_var($filters[$booleanKey], FILTER_VALIDATE_BOOLEAN);
            }
        }

        foreach (['month', 'year', 'max_price', 'min_price'] as $numericKey) {
            if (isset($filters[$numericKey]) && $filters[$numericKey] !== null) {
                $filters[$numericKey] = is_numeric($filters[$numericKey]) ? (int) $filters[$numericKey] : null;
            }
        }

        if (isset($filters['max_price']) && (float) $filters['max_price'] > 0 && (float) $filters['max_price'] < 1000 && Str::contains(Str::lower($prompt), ['ribu', 'rb', ' k'])) {
            $filters['max_price'] = (int) $filters['max_price'] * 1000;
        }

        if (! in_array($filters['date_scope'] ?? 'upcoming', ['all', 'upcoming', 'past'], true)) {
            $filters['date_scope'] = 'upcoming';
        }

        if (Str::lower((string) ($filters['keyword'] ?? '')) === 'event') {
            $filters['keyword'] = null;
        }

        return $filters;
    }

    private function buildEventsPayload(Collection $events): array
    {
        return $events->map(function ($event) {
            $availableTickets = $event->tickets->where('available_qty', '>', 0)->sortBy('price')->values();
            $ticketsForPrice = $availableTickets->isNotEmpty() ? $availableTickets : $event->tickets->sortBy('price')->values();
            $lowestTicket = $ticketsForPrice->first();

            $distance = $event->getAttributes()['distance_km'] ?? null;

            return [
                'title' => $event->title,
                'link_text' => $this->makeSafeMarkdownLinkText($event->title),
                'url' => route('events.show', $event->slug),
                'slug' => $event->slug,
                'category' => $event->category?->name,
                'date' => optional($event->start_time)->translatedFormat('d M Y H:i'),
                'location_name' => $event->location_name,
                'address' => $event->address,
                'distance_km' => $distance !== null ? (float) $distance : null,
                'lowest_price' => $lowestTicket ? (float) $lowestTicket->price : null,
                'available_ticket_count' => (int) $availableTickets->sum('available_qty'),
                'tickets' => $ticketsForPrice->take(3)->map(fn ($ticket) => [
                    'name' => $ticket->name,
                    'price' => (float) $ticket->price,
                    'available_qty' => (int) $ticket->available_qty,
                ])->values(),
            ];
        })->values()->all();
    }

    private function buildEventCards(Collection $events): array
    {
        return $events->map(function ($event) {
            $availableTickets = $event->tickets->where('available_qty', '>', 0)->sortBy('price')->values();
            $ticketsForPrice = $availableTickets->isNotEmpty() ? $availableTickets : $event->tickets->sortBy('price')->values();
            $lowestTicket = $ticketsForPrice->first();

            $bannerUrl = null;
            if (filled($event->banner_path)) {
                $bannerUrl = \Illuminate\Support\Str::startsWith($event->banner_path, ['http://', 'https://'])
                    ? $event->banner_path
                    : \Illuminate\Support\Facades\Storage::url(ltrim(preg_replace('#^/?storage/#', '', $event->banner_path), '/'));
            }

            $distance = $event->getAttributes()['distance_km'] ?? null;

            return [
                'id'           => $event->id,
                'title'        => $event->title,
                'date'         => optional($event->start_time)->translatedFormat('d M Y, H:i'),
                'location'     => $event->location_name,
                'distance_km'  => $distance !== null ? (float) $distance : null,
                'lowest_price' => $lowestTicket ? (float) $lowestTicket->price : null,
                'banner_url'   => $bannerUrl,
                'category'     => $event->category?->name,
            ];
        })->values()->all();
    }

    private function makeSafeMarkdownLinkText(string $title): string
    {
        return trim(str_replace(['[', ']'], ['(', ')'], $title));
    }

    private function normalizeCategoryName(string $category): string
    {
        $lowerCategory = Str::lower($category);

        return match (true) {
            Str::contains($lowerCategory, ['konser', 'music', 'musik', 'festival']) => 'Music Concert',
            Str::contains($lowerCategory, ['tech', 'teknologi', 'conference', 'konferensi']) => 'Tech Conference',
            Str::contains($lowerCategory, ['workshop', 'kelas', 'pelatihan']) => 'Workshop',
            Str::contains($lowerCategory, ['sports', 'sport', 'olahraga']) => 'Sports',
            Str::contains($lowerCategory, ['art', 'seni', 'pameran']) => 'Art Exhibition',
            default => $category,
        };
    }

    private function hasStrictFilters(array $filters): bool
    {
        return filled($filters['keyword'] ?? null)
            || filled($filters['max_price'] ?? null)
            || filled($filters['min_price'] ?? null)
            || ($filters['free_only'] ?? false)
            || ($filters['available_only'] ?? false);
    }

    /**
     * Jarak dua titik koordinat (km) memakai formula Haversine.
     */
    private function haversineKm(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371; // km

        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;

        return $earthRadius * 2 * asin(min(1.0, sqrt($a)));
    }

}
