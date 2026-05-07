<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventCategory;
use App\Services\GoogleMapsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

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

    public function store(Request $request, GoogleMapsService $googleMaps)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:event_categories,id',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'description' => 'required|string',
            'start_time' => 'required|date|after:now',
            'end_time' => 'required|date|after:start_time',
            'location_name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'tickets' => 'required|array|min:1',
            'tickets.*.name' => 'required|string|max:255',
            'tickets.*.price' => 'required|numeric|min:0',
            'tickets.*.quota' => 'required|integer|min:1',
            'portfolio' => 'nullable|file|mimes:pdf,jpeg,png,jpg|max:5120',
            'proposal' => 'nullable|file|mimes:pdf|max:10240',
            'action' => 'nullable|in:draft,submit',
        ]);

        if (blank($validated['latitude'] ?? null) || blank($validated['longitude'] ?? null)) {
            $query = trim(($validated['location_name'] ?? '') . ' ' . ($validated['address'] ?? ''));
            $geocoded = $googleMaps->geocode($query);

            if (! $geocoded || blank($geocoded['latitude']) || blank($geocoded['longitude'])) {
                throw ValidationException::withMessages([
                    'location_name' => 'Lokasi tidak berhasil dikenali. Pilih lokasi dari saran Google Maps atau isi nama lokasi yang lebih spesifik.',
                ]);
            }

            $validated['latitude'] = $geocoded['latitude'];
            $validated['longitude'] = $geocoded['longitude'];
            $validated['address'] = $validated['address'] ?: ($geocoded['address'] ?? null);
        }

        if (blank($validated['address'] ?? null)) {
            throw ValidationException::withMessages([
                'address' => 'Alamat lokasi belum terisi. Pilih hasil lokasi dari Google Maps agar alamat terisi otomatis.',
            ]);
        }

        $bannerPath = null;
        if ($request->hasFile('banner')) {
            $bannerPath = $request->file('banner')->store('events/banners', 'public');
        }

        $portfolioPath = null;
        if ($request->hasFile('portfolio')) {
            $portfolioPath = $request->file('portfolio')->store('events/portfolios', 'public');
        }

        $proposalPath = null;
        if ($request->hasFile('proposal')) {
            $proposalPath = $request->file('proposal')->store('events/proposals', 'public');
        }

        DB::transaction(function () use ($request, $validated, $bannerPath, $portfolioPath, $proposalPath) {
            $event = $request->user()->events()->create([
                'category_id' => $validated['category_id'],
                'title' => $validated['title'],
                'slug' => Str::slug($validated['title']) . '-' . uniqid(),
                'banner_path' => $bannerPath,
                'portfolio_path' => $portfolioPath,
                'proposal_path' => $proposalPath,
                'description' => $request->description,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'location_name' => $request->location_name,
                'address' => $request->address,
                'status' => 'pending_review',
            ]);

            foreach ($validated['tickets'] as $ticketData) {
                $event->tickets()->create([
                    'name' => $ticketData['name'],
                    'price' => $ticketData['price'],
                    'quota' => $ticketData['quota'],
                    'available_qty' => $ticketData['quota'],
                ]);
            }
        });

        return redirect()->route('organizer.events.index')->with(
            'success',
            $request->input('action') === 'draft'
                ? 'Event berhasil disimpan sebagai draft.'
                : 'Event berhasil diajukan untuk review admin.'
        );
    }

    public function show(string $id)
    {
        $event = auth()->user()->events()->with('tickets', 'category')->findOrFail($id);
        return view('organizer.events.show', compact('event'));
    }

    public function edit(string $id)
    {
        $event = auth()->user()->events()->with('tickets', 'category')->findOrFail($id);

        if ($event->status === 'published') {
            return back()->with('error', 'Event yang sudah published tidak dapat diedit.');
        }

        $categories = EventCategory::all();
        return view('organizer.events.edit', compact('event', 'categories'));
    }

    public function update(Request $request, string $id)
    {
        $event = auth()->user()->events()->with('tickets')->findOrFail($id);

        if ($event->status === 'published') {
            return back()->with('error', 'Cannot edit published events directly.');
        }

        if ($request->input('action') === 'submit') {
            $event->update(['status' => 'pending_review']);
            return redirect()->route('organizer.events.index')->with('success', 'Event berhasil diajukan untuk review.');
        }

        $validated = $request->validate([
            'title'         => 'required|string|max:255',
            'category_id'   => 'required|exists:event_categories,id',
            'banner'        => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'description'   => 'required|string',
            'start_time'    => 'required|date',
            'end_time'      => 'required|date|after:start_time',
            'location_name' => 'required|string|max:255',
            'address'       => 'nullable|string',
            'tickets'       => 'nullable|array',
            'tickets.*.id'    => 'required|integer',
            'tickets.*.name'  => 'required|string|max:255',
            'tickets.*.price' => 'required|numeric|min:0',
            'tickets.*.quota' => 'required|integer|min:1',
        ]);

        $updateData = [
            'title'         => $validated['title'],
            'category_id'   => $validated['category_id'],
            'description'   => $validated['description'],
            'start_time'    => $validated['start_time'],
            'end_time'      => $validated['end_time'],
            'location_name' => $validated['location_name'],
            'address'       => $validated['address'] ?? $event->address,
            'status'        => 'draft',
        ];

        if ($request->hasFile('banner')) {
            $updateData['banner_path'] = $request->file('banner')->store('events/banners', 'public');
        }

        $event->update($updateData);

        if (!empty($validated['tickets'])) {
            foreach ($validated['tickets'] as $ticketData) {
                $ticket = $event->tickets->firstWhere('id', $ticketData['id']);
                if ($ticket) {
                    $soldQty = $ticket->quota - $ticket->available_qty;
                    $newQuota = max((int) $ticketData['quota'], $soldQty);
                    $ticket->update([
                        'name'          => $ticketData['name'],
                        'price'         => $ticketData['price'],
                        'quota'         => $newQuota,
                        'available_qty' => $newQuota - $soldQty,
                    ]);
                }
            }
        }

        return redirect()->route('organizer.events.index')->with('success', 'Event berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $event = auth()->user()->events()->findOrFail($id);
        $event->delete();
        return back()->with('success', 'Event deleted.');
    }

    public function attendees(Event $event)
    {
        if ($event->organizer_id !== auth()->id()) {
            abort(403);
        }

        $orderItems = \App\Models\OrderItem::with(['order.user', 'ticket', 'eTickets'])
            ->whereHas('ticket', function ($query) use ($event) {
                $query->where('event_id', $event->id);
            })->latest()->get();

        return view('organizer.events.attendees', compact('event', 'orderItems'));
    }

    public function checkinView(Event $event)
    {
        if ($event->organizer_id !== auth()->id()) {
            abort(403);
        }
        return view('organizer.events.checkin', compact('event'));
    }

    public function checkin(Request $request, Event $event)
    {
        if ($event->organizer_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'ticket_code' => 'required|string'
        ]);

        $eTicket = \App\Models\ETicket::with(['orderItem.ticket.event', 'orderItem.order.user'])
            ->where('ticket_code', $request->ticket_code)
            ->first();

        if (!$eTicket) {
            return response()->json(['success' => false, 'message' => 'Ticket code not found']);
        }

        if ($eTicket->orderItem->ticket->event_id !== $event->id) {
            return response()->json(['success' => false, 'message' => 'This ticket is for a different event']);
        }

        if ($eTicket->status !== 'active') {
            return response()->json([
                'success' => false, 
                'message' => 'Ticket is ' . $eTicket->status . ' (Check-in time: ' . ($eTicket->used_at ? $eTicket->used_at->format('d M Y H:i:s') : '-') . ')'
            ]);
        }

        // Validate if order is paid (just in case)
        if ($eTicket->orderItem->order->status !== 'paid') {
            return response()->json(['success' => false, 'message' => 'Order for this ticket is not fully paid.']);
        }

        $eTicket->update([
            'status' => 'used',
            'used_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'buyer_name' => $eTicket->orderItem->order->user->name,
            'ticket_name' => $eTicket->orderItem->ticket->name,
            'checkin_time' => $eTicket->used_at->format('d M Y H:i:s')
        ]);
    }
}
