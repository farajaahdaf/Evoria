<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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

        // Fetch published events to inject as context, including tickets for prices
        $events = \App\Models\Event::with(['category', 'tickets'])->where('status', 'published')->get(['id', 'title', 'start_time', 'location_name', 'description', 'category_id', 'slug']);
        
        $appUrl = url('/event/');
        
        $systemPrompt = "You are a helpful and friendly customer service assistant for 'Evoria', an event ticketing platform. You help attendees find events based on their criteria. 
        Here is the current list of published events in the database in JSON format. 
        ONLY recommend events from this list. If the user asks for something not in the list, tell them there are no matches. Do not invent events.
        
        CRITICAL INSTRUCTIONS:
        1. When recommending an event, ALWAYS mention its ticket prices (based on the 'tickets' array in the JSON). If price is 0, it means it's FREE.
        2. When recommending an event, ALWAYS provide a clickable link to it using this Markdown format: [Event Title]({$appUrl}/slug-of-the-event)
        3. Keep your answers concise but helpful. Use Markdown formatting for lists and bold text where appropriate.

        Events List: \n" . json_encode($events);

        try {
            $response = \Illuminate\Support\Facades\Http::withToken($apiKey)->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-4o',
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $request->prompt]
                ],
                'max_tokens' => 150,
                'temperature' => 0.5
            ]);

            if ($response->successful()) {
                $reply = $response->json()['choices'][0]['message']['content'];
                
                // Keep a log
                \App\Models\ChatbotLog::create([
                    'user_id' => $request->user()->id,
                    'prompt' => $request->prompt,
                    'response' => $reply
                ]);

                return response()->json(['response' => $reply]);
            }
            
            return response()->json(['response' => "Sorry, the AI service returned an error. Try again later."]);

        } catch (\Exception $e) {
            return response()->json(['response' => "An error occurred while connecting to my brain. Details: " . $e->getMessage()]);
        }
    }
}
