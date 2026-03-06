<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\Ticket;
use App\Models\User;
use App\Models\EventCategory;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Str;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        $organizer = User::where('role', 'organizer')->first();
        $category = EventCategory::first();

        if (!$organizer || !$category) return;

        $event = Event::create([
            'organizer_id' => $organizer->id,
            'category_id' => $category->id,
            'title' => 'Laravel Tech Conference 2026',
            'slug' => Str::slug('Laravel Tech Conference 2026'),
            'description' => 'The biggest tech conference in town focusing on Laravel, PHP, and modern web architectures.',
            'start_time' => Carbon::now()->addDays(10),
            'end_time' => Carbon::now()->addDays(11),
            'location_name' => 'Jakarta Convention Center',
            'address' => 'Jl. Gatot Subroto',
            'status' => 'published',
        ]);

        Ticket::create([
            'event_id' => $event->id,
            'name' => 'Early Bird Access',
            'price' => 150000,
            'quota' => 100,
            'available_qty' => 100,
        ]);

        Ticket::create([
            'event_id' => $event->id,
            'name' => 'VIP Tickets',
            'price' => 500000,
            'quota' => 20,
            'available_qty' => 20,
        ]);
    }
}
