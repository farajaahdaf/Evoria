<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Event;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $query = Event::with('category', 'organizer', 'tickets')->where('status', 'published');

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('location_name', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%");
            });
        }

        if ($request->filled('city')) {
            $city = $request->city;
            $query->where(function ($q) use ($city) {
                $q->where('location_name', 'like', "%{$city}%")
                  ->orWhere('address', 'like', "%{$city}%");
            });
        }

        if ($request->filled('date_from')) {
            $query->where('start_time', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('start_time', '<=', $request->date_to . ' 23:59:59');
        }

        if ($request->filled('min_price')) {
            $query->whereHas('tickets', fn ($q) => $q->where('price', '>=', (float) $request->min_price));
        }

        if ($request->filled('max_price')) {
            $maxPrice = (float) $request->max_price;
            if ($maxPrice === 0.0) {
                $query->whereHas('tickets', fn ($q) => $q->where('price', 0));
            } else {
                $query->whereHas('tickets', fn ($q) => $q->where('price', '<=', $maxPrice));
            }
        }

        $events = $query->latest()->paginate(10);

        return response()->json([
            'status' => 'success',
            'data' => $events,
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
