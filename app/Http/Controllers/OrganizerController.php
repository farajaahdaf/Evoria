<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OrganizerController extends Controller
{
    public function pending(Request $request)
    {
        abort_unless($request->user()->role === 'organizer', 403);

        $profile = $request->user()->organizerProfile;
        return view('organizer.pending', compact('profile'));
    }

    public function dashboard(Request $request)
    {
        $user = $request->user();
        $eventsCount = $user->events()->count();
        $publishedCount = $user->events()->where('status', 'published')->count();
        
        $totalTicketsSold = \App\Models\OrderItem::whereHas('ticket.event', function ($query) use ($user) {
            $query->where('organizer_id', $user->id);
        })->whereHas('order', function ($query) {
            $query->where('status', 'paid');
        })->sum('quantity');

        $totalRevenue = \App\Models\OrderItem::whereHas('ticket.event', function ($query) use ($user) {
            $query->where('organizer_id', $user->id);
        })->whereHas('order', function ($query) {
            $query->where('status', 'paid');
        })->sum('subtotal');

        $recentEvents = $user->events()->latest()->take(5)->get();

        return view('organizer.dashboard', compact(
            'eventsCount', 
            'publishedCount', 
            'totalTicketsSold', 
            'totalRevenue',
            'recentEvents'
        ));
    }
}
