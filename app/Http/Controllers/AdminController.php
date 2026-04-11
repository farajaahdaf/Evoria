<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        $pendingOrganizers = \App\Models\OrganizerProfile::where('status', 'pending')->count();
        $pendingEvents = \App\Models\Event::where('status', 'pending_review')->count();
        
        $totalUsers = \App\Models\User::where('role', 'attendee')->count();
        $totalOrganizers = \App\Models\OrganizerProfile::where('status', 'verified')->count();
        $totalRevenue = \App\Models\Order::where('status', 'paid')->sum('total_amount');
        $totalEvents = \App\Models\Event::where('status', 'published')->count();

        return view('admin.dashboard', compact(
            'pendingOrganizers', 
            'pendingEvents', 
            'totalUsers', 
            'totalOrganizers', 
            'totalRevenue', 
            'totalEvents'
        ));
    }

    public function verifyOrganizers()
    {
        $organizers = \App\Models\OrganizerProfile::with('user')->where('status', 'pending')->get();
        // Fallback to simple dump if view doesn't exist yet for clean starter
        if (!view()->exists('admin.organizers')) return $organizers;
        return view('admin.organizers', compact('organizers'));
    }

    public function showOrganizer($id)
    {
        $organizer = \App\Models\OrganizerProfile::with('user')->findOrFail($id);
        return view('admin.organizer-detail', compact('organizer'));
    }

    public function approveOrganizer(Request $request, $id)
    {
        $profile = \App\Models\OrganizerProfile::findOrFail($id);
        $profile->update(['status' => 'verified']);
        $profile->user?->update(['role' => 'organizer']);
        return redirect()->route('admin.organizers')->with('success', 'Organizer verified successfully.');
    }

    public function rejectOrganizer(Request $request, $id)
    {
        $profile = \App\Models\OrganizerProfile::findOrFail($id);
        $profile->update(['status' => 'rejected']);
        $profile->user?->update(['role' => 'attendee']);
        return redirect()->route('admin.organizers')->with('success', 'Organizer application rejected.');
    }

    public function approveEvents()
    {
        $events = \App\Models\Event::with('organizer')->where('status', 'pending_review')->get();
        if (!view()->exists('admin.events')) return $events;
        return view('admin.events', compact('events'));
    }

    public function publishEvent(Request $request, $id)
    {
        $event = \App\Models\Event::findOrFail($id);
        $event->update(['status' => 'published']);
        return back()->with('success', 'Event approved and published successfully.');
    }

    public function rejectEvent(Request $request, $id)
    {
        $event = \App\Models\Event::findOrFail($id);
        $event->update(['status' => 'rejected']);
        return back()->with('success', 'Event application rejected.');
    }

    public function transactions()
    {
        $orders = \App\Models\Order::with(['user', 'orderItems.ticket.event'])->latest()->get();
        return view('admin.transactions', compact('orders'));
    }
}
