<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\EventCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ChatbotControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config(['services.openai.api_key' => 'testing-key']);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    public function test_chatbot_can_list_all_published_events_from_database(): void
    {
        $this->fakeOpenAiResponses([
            'intent' => 'event_search',
            'keyword' => null,
            'city' => null,
            'max_price' => null,
            'min_price' => null,
            'free_only' => false,
            'available_only' => false,
            'date_scope' => 'all',
            'month' => null,
            'year' => null,
            'category' => null,
            'list_all' => true,
        ], 'Saya menemukan 2 event: Jakarta Jazz Night dan Pontianak Creative Fest.');

        $category = EventCategory::create([
            'name' => 'Music Concert',
            'slug' => 'music-concert',
        ]);
        $organizer = User::factory()->create(['role' => 'organizer']);

        $this->createEventWithTicket($organizer, $category, [
            'title' => 'RIIZE - 2026 RIIZE Concert Tour [RIIZING LOUD]',
            'slug' => 'jakarta-jazz-night',
            'location_name' => 'Istora Senayan',
            'address' => 'Jakarta',
        ]);
        $this->createEventWithTicket($organizer, $category, [
            'title' => 'Pontianak Creative Fest',
            'slug' => 'pontianak-creative-fest',
            'location_name' => 'Auditorium UNTAN',
            'address' => 'Pontianak',
        ]);

        $response = $this->postJson(route('chat'), [
            'prompt' => 'list semua event yang ada',
        ]);

        $response->assertOk();
        $reply = $response->json('response');

        $this->assertStringContainsString('Saya menemukan 2 event', $reply);
        $this->assertStringContainsString('Jakarta Jazz Night', $reply);
        $this->assertStringContainsString('Pontianak Creative Fest', $reply);

        Http::assertSent(function ($request) {
            $payload = $request->data();
            $systemPrompt = $payload['messages'][0]['content'] ?? '';

            return str_contains($systemPrompt, 'exact_matches')
                && str_contains($systemPrompt, 'RIIZE - 2026 RIIZE Concert Tour [RIIZING LOUD]')
                && str_contains($systemPrompt, 'RIIZE - 2026 RIIZE Concert Tour (RIIZING LOUD)')
                && str_contains($systemPrompt, 'Pontianak Creative Fest');
        });
    }

    public function test_chatbot_can_filter_events_by_city(): void
    {
        $this->fakeOpenAiResponses([
            'intent' => 'event_search',
            'keyword' => null,
            'city' => 'Pontianak',
            'max_price' => null,
            'min_price' => null,
            'free_only' => false,
            'available_only' => false,
            'date_scope' => 'all',
            'month' => null,
            'year' => null,
            'category' => null,
            'list_all' => true,
        ], 'Saya menemukan 1 event di Pontianak: Pontianak Creative Fest.');

        $category = EventCategory::create([
            'name' => 'Music Concert',
            'slug' => 'music-concert',
        ]);
        $organizer = User::factory()->create(['role' => 'organizer']);

        $this->createEventWithTicket($organizer, $category, [
            'title' => 'Jakarta Jazz Night',
            'slug' => 'jakarta-jazz-night',
            'location_name' => 'Istora Senayan',
            'address' => 'Jakarta',
        ]);
        $this->createEventWithTicket($organizer, $category, [
            'title' => 'Pontianak Creative Fest',
            'slug' => 'pontianak-creative-fest',
            'location_name' => 'Auditorium UNTAN',
            'address' => 'Pontianak',
        ]);

        $response = $this->postJson(route('chat'), [
            'prompt' => 'list event yang ada di Pontianak',
        ]);

        $response->assertOk();
        $reply = $response->json('response');

        $this->assertStringContainsString('Pontianak Creative Fest', $reply);
        $this->assertStringNotContainsString('Jakarta Jazz Night', $reply);

        Http::assertSent(function ($request) {
            $payload = $request->data();
            $systemPrompt = $payload['messages'][0]['content'] ?? '';

            return str_contains($systemPrompt, 'exact_matches')
                && str_contains($systemPrompt, 'Pontianak Creative Fest')
                && ! str_contains($systemPrompt, 'Jakarta Jazz Night');
        });
    }

    public function test_chatbot_returns_unavailable_when_openai_request_fails(): void
    {
        Http::fake([
            'api.openai.com/*' => Http::response([], 500),
        ]);

        $response = $this->postJson(route('chat'), [
            'prompt' => 'list semua event yang ada',
        ]);

        $response->assertStatus(503);
        $response->assertJson([
            'response' => 'Maaf, layanan AI sedang bermasalah. Coba lagi sebentar lagi.',
        ]);
    }

    private function createEventWithTicket(User $organizer, EventCategory $category, array $overrides): Event
    {
        $event = Event::create(array_merge([
            'organizer_id' => $organizer->id,
            'category_id' => $category->id,
            'title' => 'Demo Event',
            'slug' => 'demo-event',
            'description' => 'Demo event description.',
            'start_time' => now()->setDate(2026, 5, 10)->setTime(19, 0),
            'end_time' => now()->setDate(2026, 5, 10)->setTime(22, 0),
            'location_name' => 'Demo Venue',
            'address' => 'Demo City',
            'status' => 'published',
        ], $overrides));

        $event->tickets()->create([
            'name' => 'Regular',
            'price' => 150000,
            'quota' => 20,
            'available_qty' => 20,
        ]);

        return $event;
    }

    private function fakeOpenAiResponses(array $filters, string $answer): void
    {
        $callCount = 0;

        Http::fake(function () use (&$callCount, $filters, $answer) {
            $callCount++;

            if ($callCount === 1) {
                return Http::response([
                    'choices' => [
                        ['message' => ['content' => json_encode($filters)]],
                    ],
                ]);
            }

            return Http::response([
                'choices' => [
                    ['message' => ['content' => $answer]],
                ],
            ]);
        });
    }
}
