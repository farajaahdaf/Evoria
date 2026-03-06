<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Event;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $query = Event::with('category', 'organizer')->where('status', 'published');

        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $events = $query->latest()->paginate(10);
        return response()->json([
            'status' => 'success',
            'data' => $events
        ]);
    }

    public function show($id)
    {
        $event = Event::with('category', 'organizer', 'tickets')->where('status', 'published')->findOrFail($id);
        
        return response()->json([
            'status' => 'success',
            'data' => $event
        ]);
    }
}
