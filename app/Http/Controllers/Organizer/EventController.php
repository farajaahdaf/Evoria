<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\EventCategory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $events = $request->user()->events()->latest()->paginate(10);
        return view('organizer.events.index', compact('events'));
    }

    public function create()
    {
        $categories = EventCategory::all();
        return view('organizer.events.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:event_categories,id',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'description' => 'required|string',
            'start_time' => 'required|date|after:now',
            'end_time' => 'required|date|after:start_time',
            'location_name' => 'required|string|max:255',
            'address' => 'required|string',
            'tickets' => 'required|array|min:1',
            'tickets.*.name' => 'required|string|max:255',
            'tickets.*.price' => 'required|numeric|min:0',
            'tickets.*.quota' => 'required|integer|min:1',
        ]);

        $bannerPath = null;
        if ($request->hasFile('banner')) {
            $bannerPath = $request->file('banner')->store('events', 'public');
        }

        DB::transaction(function () use ($request, $bannerPath) {
            $event = $request->user()->events()->create([
                'category_id' => $request->category_id,
                'title' => $request->title,
                'slug' => Str::slug($request->title) . '-' . uniqid(),
                'banner_path' => $bannerPath,
                'description' => $request->description,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'location_name' => $request->location_name,
                'address' => $request->address,
                'status' => 'draft',
            ]);

            foreach ($request->tickets as $ticketData) {
                $event->tickets()->create([
                    'name' => $ticketData['name'],
                    'price' => $ticketData['price'],
                    'quota' => $ticketData['quota'],
                    'available_qty' => $ticketData['quota'],
                ]);
            }
        });

        return redirect()->route('organizer.events.index')->with('success', 'Event and tickets successfully created as Draft!');
    }

    public function show(string $id)
    {
        $event = auth()->user()->events()->with('tickets', 'category')->findOrFail($id);
        return view('organizer.events.show', compact('event'));
    }

    public function edit(string $id)
    {
        $event = auth()->user()->events()->findOrFail($id);
        $categories = EventCategory::all();
        return view('organizer.events.edit', compact('event', 'categories'));
    }

    public function update(Request $request, string $id)
    {
        $event = auth()->user()->events()->findOrFail($id);
        
        if ($event->status === 'published') {
            return back()->with('error', 'Cannot edit published events directly.');
        }

        $event->update([
            'status' => $request->action === 'submit' ? 'pending_review' : 'draft'
        ]);

        return redirect()->route('organizer.events.index')->with('success', 'Event updated successfully.');
    }

    public function destroy(string $id)
    {
        $event = auth()->user()->events()->findOrFail($id);
        $event->delete();
        return back()->with('success', 'Event deleted.');
    }
}
