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

        return view('organizer.dashboard', compact('eventsCount'));
    }
}
