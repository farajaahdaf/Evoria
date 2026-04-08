<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class ChatbotController extends Controller
{
    public function chat(Request $request)
    {
        $request->validate(['prompt' => 'required|string']);

        $apiKey = env('OPENAI_API_KEY');
        if (empty($apiKey)) {
            return response()->json([
                'response' => "I am an AI assistant. However, my 'OPENAI_API_KEY' environment variable hasn't been set up yet, so I cannot process your request. Please configure it in the .env file!"
            ]);
        }

        // STEP 1: Ask OpenAI to extract search filters from the user's prompt
        $currentDate = now()->format('Y-m-d H:i:s');
        $filterPrompt = "You are an AI assistant helping to build a database query for an event ticketing app called 'Evoria'.
        Extract the search criteria from the user's prompt and return ONLY a valid JSON object. Do not include markdown formatting or extra text.
        
        Today's Date and Time Context: {$currentDate}
        
        JSON Structure needed:
        {
            \"keyword\": \"(string) ONLY extract specific proper nouns, artists, event names, or specific locations. IGNORE generic conversational words like 'event', 'acara', 'konser', 'cari', 'dong', 'saya', 'mau'. Return null if no specific keyword.\",
            \"max_price\": \"(number) maximum ticket price mentioned (e.g., 0 for free/gratis, 100000), or null if none\",
            \"month\": \"(integer) 1-12 if a specific month is mentioned (e.g. 'september' -> 9), or null\",
            \"year\": \"(integer) e.g. " . now()->format('Y') . " if mentioned or implied by the month, or null\",
            \"category\": \"(string) category name if mentioned (e.g. 'konser', 'workshop', 'festival', 'conference'), or null\"
        }
        
        Example Input: 'saya mau event workshop'
        Example Output: {\"keyword\": null, \"max_price\": null, \"month\": null, \"year\": null, \"category\": \"workshop\"}

        Example Input: 'cari konser tulus di jakarta barat bulan september gratis'
        Example Output: {\"keyword\": \"tulus jakarta barat\", \"max_price\": 0, \"month\": 9, \"year\": " . now()->format('Y') . ", \"category\": \"konser\"}
        
        User Prompt: '{$request->prompt}'";

        try {
            $filterResponse = \Illuminate\Support\Facades\Http::withToken($apiKey)->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-4o-mini', // Use faster/cheaper model for simple text extraction
                'messages' => [['role' => 'user', 'content' => $filterPrompt]],
                'temperature' => 0.1
            ]);

            if (!$filterResponse->successful()) {
                throw new \Exception("Step 1 (Extraction) failed.");
            }

            // Clean the response slightly in case the AI wraps it in markdown blocks
            $jsonString = $filterResponse->json()['choices'][0]['message']['content'];
            $jsonString = str_replace(['```json', '```'], '', $jsonString);
            $filters = json_decode(trim($jsonString), true);

            // STEP 2: Query the database with the extracted filters (Max 10 results to save tokens)
            $query = \App\Models\Event::with(['category', 'tickets'])->where('status', 'published');

            if (isset($filters['keyword']) && $filters['keyword']) {
                $keywordChunks = explode(' ', $filters['keyword']);
                foreach($keywordChunks as $chunk) {
                    if (strlen($chunk) > 2) { // avoid matching 'di', 'ke', etc too broadly
                        $query->where(function($q) use ($chunk) {
                            $q->where('title', 'like', "%{$chunk}%")
                              ->orWhere('description', 'like', "%{$chunk}%")
                              ->orWhere('location_name', 'like', "%{$chunk}%");
                        });
                    }
                }
            }
            
            if (isset($filters['month']) && $filters['month']) {
                $query->whereMonth('start_time', $filters['month']);
            }
            if (isset($filters['year']) && $filters['year']) {
                $query->whereYear('start_time', $filters['year']);
            }
            if (isset($filters['category']) && $filters['category']) {
                $query->whereHas('category', function($q) use ($filters) {
                    $q->where('name', 'like', "%{$filters['category']}%");
                });
            }

            $events = $query->take(10)->get(['id', 'title', 'start_time', 'location_name', 'description', 'category_id', 'slug']);
            
            // Further filter by price locally if requested
            if (isset($filters['max_price']) && $filters['max_price'] !== null) {
                $maxPrice = (float) $filters['max_price'];
                $events = $events->filter(function($event) use ($maxPrice) {
                    if ($event->tickets->isEmpty()) {
                        return $maxPrice >= 0;
                    }
                    // Check if any ticket tier matches the max price condition
                    foreach ($event->tickets as $ticket) {
                        if ((float)$ticket->price <= $maxPrice) return true;
                    }
                    return false;
                });
            }

            // STEP 3: Context Injection using ONLY the filtered results
            $eventBaseUrl = rtrim(URL::to('/event'), '/');
            $systemPrompt = "You are a helpful and friendly customer service assistant for 'Evoria', an event marketplace.
            Here is a highly filtered list of currently published events matching the user's intent. Do NOT invent events.
            If the list is empty `[]`, apologize and say no matching events are available right now.
            
            CRITICAL INSTRUCTIONS:
            1. Mention ticket prices (from 'tickets' array). If price is 0, it means it's FREE.
            2. ALWAYS provide a clickable link using Markdown format: [Event Title]({$eventBaseUrl}/event-slug)
            3. Keep answers concise, complete every event card/listing you start, and use Markdown formatting for lists and bold text.
            4. Never cut off mid-sentence, mid-list, or mid-link.

            Filtered Events List: \n" . json_encode($events->values()->all());

            $finalResponse = \Illuminate\Support\Facades\Http::withToken($apiKey)->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-4o',
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $request->prompt]
                ],
                'max_tokens' => 500,
                'temperature' => 0.4
            ]);

            if ($finalResponse->successful()) {
                $reply = $finalResponse->json()['choices'][0]['message']['content'];
                
                \App\Models\ChatbotLog::create([
                    'user_id' => $request->user() ? $request->user()->id : null,
                    'prompt' => $request->prompt,
                    'response' => $reply
                ]);

                return response()->json(['response' => $reply]);
            }
            
            return response()->json(['response' => "Sorry, the AI service returned an error. Try again later."]);

        } catch (\Exception $e) {
            return response()->json(['response' => "An error occurred while analyzing the database. Details: " . $e->getMessage()]);
        }
    }
}
