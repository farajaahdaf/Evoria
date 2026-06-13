<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Event;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'lat' => 'nullable|numeric|between:-90,90',
            'lng' => 'nullable|numeric|between:-180,180',
        ]);

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

        // Sortir "terdekat": hitung jarak (Haversine) di DB atas SEMUA event yang
        // cocok, lalu urutkan dari paling dekat. Konsisten dengan chatbot.
        $lat = $request->filled('lat') ? (float) $request->lat : null;
        $lng = $request->filled('lng') ? (float) $request->lng : null;
        $nearest = filter_var($request->input('sort_nearest', false), FILTER_VALIDATE_BOOLEAN)
            && $lat !== null && $lng !== null;

        if ($nearest) {
            $haversine = '6371 * acos(LEAST(1.0, '
                . 'cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) '
                . '+ sin(radians(?)) * sin(radians(latitude))))';

            $events = $query
                ->whereNotNull('latitude')
                ->whereNotNull('longitude')
                // Hanya event mendatang — selaras dengan chatbot, agar "terdekat"
                // tidak menampilkan event yang sudah lewat.
                ->where('start_time', '>=', now()->startOfDay())
                ->select('events.*')
                ->selectRaw("ROUND($haversine, 1) as distance_km", [$lat, $lng, $lat])
                ->orderBy('distance_km')
                ->paginate(10);
        } else {
            $events = $query->latest()->paginate(10);
        }

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
