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
        $request->validate(['prompt' => 'required|string|max:1000']);

        $prompt = trim($request->prompt);
        $apiKey = config('services.openai.api_key');

        if (empty($apiKey)) {
            return response()->json([
                'response' => 'Evoria AI belum aktif karena OPENAI_API_KEY belum diatur di file .env.',
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

            return response()->json(['response' => $reply]);
        }

        if (($filters['intent'] ?? 'event_search') === 'general_help') {
            return $this->answerGeneralQuestion($request, $prompt, $apiKey);
        }

        [$events, $fallbackEvents, $totalMatches] = $this->findEvents($filters);

        try {
            $reply = $this->generateAiEventAnswer($prompt, $filters, $events, $fallbackEvents, $totalMatches, $apiKey);
            $this->logChat($request, $prompt, $reply);

            return response()->json(['response' => $reply]);
        } catch (\Throwable) {
            return response()->json([
                'response' => 'Maaf, layanan AI sedang bermasalah. Coba lagi sebentar lagi.',
            ], 503);
        }
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
  \"list_all\": \"true if user asks to list/show all events or events in a city/category without a narrow keyword\"
}

Rules:
- 'list semua event', 'daftar event', 'event apa saja', and 'event di Jakarta/Pontianak' are event_search with list_all=true.
- If user asks 'yang ada' or 'semua event', use date_scope=all and available_only=false.
- If user asks 'yang akan datang', 'upcoming', or 'tersedia', use date_scope=upcoming.
- Do not use the word 'event' itself as keyword.

Examples:
Input: list semua event yang ada
Output: {\"intent\":\"event_search\",\"keyword\":null,\"city\":null,\"max_price\":null,\"min_price\":null,\"free_only\":false,\"available_only\":false,\"date_scope\":\"all\",\"month\":null,\"year\":null,\"category\":null,\"list_all\":true}
Input: list event yang ada di Pontianak
Output: {\"intent\":\"event_search\",\"keyword\":null,\"city\":\"Pontianak\",\"max_price\":null,\"min_price\":null,\"free_only\":false,\"available_only\":false,\"date_scope\":\"all\",\"month\":null,\"year\":null,\"category\":null,\"list_all\":true}
Input: cari konser gratis di jakarta bulan september
Output: {\"intent\":\"event_search\",\"keyword\":null,\"city\":\"Jakarta\",\"max_price\":0,\"min_price\":null,\"free_only\":true,\"available_only\":true,\"date_scope\":\"upcoming\",\"month\":9,\"year\":" . now()->year . ",\"category\":\"konser\",\"list_all\":false}

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

    private function findEvents(array $filters): array
    {
        $limit = ($filters['list_all'] ?? false) ? 12 : 8;
        $query = Event::with(['category', 'tickets'])
            ->where('status', 'published');

        $this->applyEventFilters($query, $filters);

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

    private function generateAiEventAnswer(
        string $prompt,
        array $filters,
        Collection $events,
        Collection $fallbackEvents,
        int $totalMatches,
        string $apiKey
    ): string {
        $systemPrompt = "You are Evoria AI Assistant, a helpful customer service assistant for an event marketplace.
Answer in Indonesian. Use ONLY the database event data below. Do not invent events, prices, dates, links, or availability.

If exact_matches has items:
- Say how many matching events were found in total.
- List up to 8 events unless the user asks for all/list, then list up to 12.
- For every event, include a clickable Markdown link using exactly this format: [link_text](url).
- Use link_text from the event data for the link label, not title, because some event titles contain square brackets.
- After the link, you may mention the full title as plain text if needed.
- Include date, city/location, lowest price, and available ticket count.
If exact_matches is empty and fallback_events has items, explain that exact matches are unavailable and offer fallback events.
If both are empty, apologize and suggest changing city, category, month, date, or budget.

Keep the answer concise. Never cut off links.

Parsed filters: " . json_encode($filters) . "
total_matches: {$totalMatches}
exact_matches: " . json_encode($this->buildEventsPayload($events)) . "
fallback_events: " . json_encode($this->buildEventsPayload($fallbackEvents));

        $finalResponse = Http::withToken($apiKey)->post('https://api.openai.com/v1/chat/completions', [
            'model' => config('services.openai.chatbot_response_model', 'gpt-4o-mini'),
            'messages' => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => $prompt],
            ],
            'max_tokens' => 900,
            'temperature' => 0.25,
        ]);

        if (! $finalResponse->successful()) {
            throw new \RuntimeException('Gagal membuat jawaban AI.');
        }

        $reply = $finalResponse->json()['choices'][0]['message']['content'] ?? null;
        if (! is_string($reply) || trim($reply) === '') {
            throw new \RuntimeException('OpenAI response is empty.');
        }

        return $reply;
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
        ], array_filter($aiFilters, fn ($value) => $value !== null && $value !== ''));

        foreach (['free_only', 'available_only', 'list_all'] as $booleanKey) {
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

            return [
                'title' => $event->title,
                'link_text' => $this->makeSafeMarkdownLinkText($event->title),
                'url' => route('events.show', $event->slug),
                'slug' => $event->slug,
                'category' => $event->category?->name,
                'date' => optional($event->start_time)->translatedFormat('d M Y H:i'),
                'location_name' => $event->location_name,
                'address' => $event->address,
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

}
