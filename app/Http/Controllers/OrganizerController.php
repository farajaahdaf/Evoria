<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OrganizerController extends Controller
{
    public function dashboard(Request $request)
    {
        $user = $request->user();
        $eventsCount = $user->events()->count();

        return view('organizer.dashboard', compact('eventsCount'));
    }
}
