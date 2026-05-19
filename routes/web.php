<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\OrganizerController;
use App\Http\Controllers\OrganizerApplicationController;
use App\Http\Controllers\Organizer\EventController;
use Illuminate\Support\Facades\Route;

Route::get('/health', function () {
    try {
        DB::connection()->getPdo();
        return response()->json(['status' => 'ok', 'db' => 'ok'], 200);
    } catch (\Throwable $e) {
        return response()->json(['status' => 'down', 'error' => $e->getMessage()], 503);
    }
});

Route::get('/', function (Illuminate\Http\Request $request) {
    $search = trim((string) $request->query('q', ''));

    if ($search !== '') {
        $events = \App\Models\Event::with(['category', 'tickets'])
            ->where('status', 'published')
            ->where(function ($query) use ($search) {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('location_name', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%")
                    ->orWhereHas('category', function ($categoryQuery) use ($search) {
                        $categoryQuery->where('name', 'like', "%{$search}%");
                    });
            })
            ->latest()
            ->take(12)
            ->get();
    } else {
        // Sort featured events by most tickets sold (paid orders only)
        $events = \App\Models\Event::with(['category', 'tickets'])
            ->where('status', 'published')
            ->leftJoinSub(
                \DB::table('order_items')
                    ->join('orders', 'orders.id', '=', 'order_items.order_id')
                    ->where('orders.status', 'paid')
                    ->join('tickets', 'tickets.id', '=', 'order_items.ticket_id')
                    ->select('tickets.event_id', \DB::raw('SUM(order_items.quantity) as total_sold'))
                    ->groupBy('tickets.event_id'),
                'sold_stats',
                'events.id',
                '=',
                'sold_stats.event_id'
            )
            ->select('events.*', \DB::raw('COALESCE(sold_stats.total_sold, 0) as total_sold'))
            ->orderByDesc('total_sold')
            ->orderByDesc('events.created_at')
            ->take(6)
            ->get();
    }

    $categories = \App\Models\EventCategory::all();
    return view('welcome', compact('events', 'categories', 'search'));
})->name('home');

Route::get('/search/suggestions', function (Illuminate\Http\Request $request) {
    $search = trim((string) $request->query('q', ''));

    if (mb_strlen($search) < 2) {
        return response()->json([]);
    }

    $events = \App\Models\Event::with(['category', 'tickets'])
        ->where('status', 'published')
        ->where(function ($query) use ($search) {
            $query->where('title', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%")
                ->orWhere('location_name', 'like', "%{$search}%")
                ->orWhere('address', 'like', "%{$search}%")
                ->orWhereHas('category', function ($categoryQuery) use ($search) {
                    $categoryQuery->where('name', 'like', "%{$search}%");
                });
        })
        ->latest()
        ->take(6)
        ->get();

    return response()->json($events->map(function ($event) {
        $minPrice = optional($event->tickets)->count() > 0 ? $event->tickets->min('price') : 0;

        return [
            'title' => $event->title,
            'url' => route('events.show', $event->slug ?? $event->id),
            'category' => $event->category?->name ?? 'Event',
            'location' => $event->location_name,
            'date' => optional($event->start_time)->translatedFormat('d M Y'),
            'price' => $minPrice > 0 ? 'Rp ' . number_format($minPrice, 0, ',', '.') : 'Gratis',
        ];
    })->values());
})->name('events.search-suggestions');

Route::get('/kategori/{slug}', function (\Illuminate\Http\Request $request, $slug) {
    // Cari berdasarkan slug, jika tidak ada fallback ke pencarian nama
    $category = \App\Models\EventCategory::where('slug', $slug)->first();

    if (!$category) {
        $category = \App\Models\EventCategory::whereRaw('LOWER(name) LIKE ?', ['%' . strtolower(str_replace('-', ' ', $slug)) . '%'])->first();
    }

    $sort = $request->query('sort', 'latest'); // latest | price_asc | price_desc
    if (! in_array($sort, ['latest', 'price_asc', 'price_desc'], true)) {
        $sort = 'latest';
    }

    $city = trim((string) $request->query('city', ''));

    $extractCity = function ($event) {
        $text = trim(collect([$event->address, $event->location_name])->filter()->implode(', '));

        if ($text === '') {
            return null;
        }

        $knownCities = [
            'Jakarta Selatan',
            'Jakarta Timur',
            'Jakarta Barat',
            'Jakarta Pusat',
            'Jakarta Utara',
            'Yogyakarta',
            'Pontianak',
            'Tangerang',
            'Surakarta',
            'Semarang',
            'Surabaya',
            'Bandung',
            'Jakarta',
            'Sleman',
            'Bekasi',
            'Depok',
            'Bogor',
            'Medan',
            'Malang',
            'Solo',
        ];

        foreach ($knownCities as $knownCity) {
            if (\Illuminate\Support\Str::contains(\Illuminate\Support\Str::lower($text), \Illuminate\Support\Str::lower($knownCity))) {
                return $knownCity;
            }
        }

        $ignoredWords = ['jalan', 'jl.', 'no.', 'hall', 'stadion', 'theater', 'ballroom', 'convention', 'exhibition', 'arena', 'banten', 'dki jakarta'];

        return collect(preg_split('/,/', $text))
            ->map(fn ($part) => trim(preg_replace('/\s+/', ' ', $part)))
            ->filter()
            ->reverse()
            ->first(function ($part) use ($ignoredWords) {
                $lowerPart = \Illuminate\Support\Str::lower($part);

                return ! collect($ignoredWords)->contains(fn ($word) => \Illuminate\Support\Str::contains($lowerPart, $word));
            });
    };

    $cityEvents = \App\Models\Event::query()
        ->where('category_id', optional($category)->id)
        ->where('status', 'published')
        ->get(['location_name', 'address']);

    $cityOptions = $cityEvents
        ->map($extractCity)
        ->filter()
        ->unique()
        ->sortBy(fn ($name) => \Illuminate\Support\Str::lower($name))
        ->values();

    $query = \App\Models\Event::with('tickets', 'organizer')
                ->where('category_id', optional($category)->id)
                ->where('status', 'published');

    if ($city !== '') {
        $query->where(function ($cityQuery) use ($city) {
            $cityQuery->where('address', 'like', "%{$city}%")
                ->orWhere('location_name', 'like', "%{$city}%");
        });
    }

    if ($sort === 'price_asc' || $sort === 'price_desc') {
        // Join dengan subquery untuk mendapatkan harga minimum tiket
        $query->leftJoinSub(
            \DB::table('tickets')
                ->select('event_id', \DB::raw('MIN(price) as min_price'))
                ->groupBy('event_id'),
            'ticket_prices',
            'events.id',
            '=',
            'ticket_prices.event_id'
        )
        ->select('events.*', \DB::raw('COALESCE(ticket_prices.min_price, 0) as min_price'))
        ->orderBy('min_price', $sort === 'price_asc' ? 'asc' : 'desc');
    } else {
        $query->latest();
    }

    $events = $query->paginate(6)->appends([
        'sort' => $sort,
        'city' => $city,
    ]);
                
    return view('categories.show', compact('category', 'events', 'sort', 'city', 'cityOptions'));
})->name('category.show');

Route::get('/event/{slug}', function ($slug) {
    $event = \App\Models\Event::with('tickets', 'organizer')->where('slug', $slug)->firstOrFail();
    return view('events.show', [
        'event' => $event,
        'midtransClientKey' => config('services.midtrans.client_key'),
        'midtransSnapJsUrl' => app(\App\Services\MidtransService::class)->getSnapJsUrl(),
        'midtransEnabled' => app(\App\Services\MidtransService::class)->isConfigured(),
        'googleMapsWebApiKey' => config('services.google_maps.web_api_key'),
    ]);
})->name('events.show');
Route::post('/chat', [\App\Http\Controllers\ChatbotController::class, 'chat'])->name('chat');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function (Illuminate\Http\Request $request) {
        if ($request->user()->role === 'admin') return redirect()->route('admin.dashboard');
        if ($request->user()->role === 'organizer') {
            if (optional($request->user()->organizerProfile)->status !== 'verified') {
                return redirect()->route('organizer.pending');
            }

            return redirect()->route('organizer.dashboard');
        }
        return redirect()->route('home');
    })->name('dashboard');

    Route::get('/organizer/pending', [OrganizerController::class, 'pending'])->name('organizer.pending');
    Route::get('/organizer/apply', [OrganizerApplicationController::class, 'create'])->name('organizer.application.create');
    Route::post('/organizer/apply', [OrganizerApplicationController::class, 'store'])
        ->middleware('role:attendee')
        ->name('organizer.application.store');

    Route::middleware('role:attendee')->prefix('attendee')->name('attendee.')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\AttendeeController::class, 'dashboard'])->name('dashboard');
        Route::post('/book/{eventId}', [\App\Http\Controllers\AttendeeController::class, 'bookTicket'])->name('book');
        Route::post('/orders/{order}/refresh-status', [\App\Http\Controllers\AttendeeController::class, 'refreshOrderStatus'])->name('orders.refresh-status');
    });

    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        
        // New management routes
        Route::get('/users', [AdminController::class, 'users'])->name('users');
        Route::get('/all-organizers', [AdminController::class, 'allOrganizers'])->name('organizers.all');
        Route::get('/all-events', [AdminController::class, 'allEvents'])->name('events.all');
        Route::get('/draft-events', [AdminController::class, 'draftEvents'])->name('events.drafts');
        Route::get('/transactions/overview', [AdminController::class, 'transactionsOverview'])->name('transactions.overview');
        Route::get('/transactions', [AdminController::class, 'transactions'])->name('transactions');

        // Existing review routes
        Route::get('/organizers', [AdminController::class, 'verifyOrganizers'])->name('organizers');
        Route::get('/organizers/{id}', [AdminController::class, 'showOrganizer'])->name('organizers.show');
        Route::post('/organizers/{id}/verify', [AdminController::class, 'approveOrganizer'])->name('organizers.verify');
        Route::post('/organizers/{id}/reject', [AdminController::class, 'rejectOrganizer'])->name('organizers.reject');
        Route::get('/events', [AdminController::class, 'approveEvents'])->name('events');
        Route::get('/events/{event:slug}', [AdminController::class, 'showEvent'])->name('events.show');
        Route::post('/events/{event:slug}/draft', [AdminController::class, 'saveEventAsDraft'])->name('events.draft');
        Route::post('/events/{event:slug}/approve', [AdminController::class, 'approveEvent'])->name('events.approve');
        Route::post('/events/{event:slug}/reject', [AdminController::class, 'rejectEvent'])->name('events.reject');
        Route::delete('/events/{event:slug}', [AdminController::class, 'deleteEvent'])->name('events.delete');
    });

    Route::middleware('role:organizer')->prefix('organizer')->name('organizer.')->group(function () {
        Route::get('/dashboard', [OrganizerController::class, 'dashboard'])->name('dashboard');
        Route::get('/balance', [OrganizerController::class, 'balance'])->name('balance');
        Route::post('/withdraw', [OrganizerController::class, 'withdraw'])->name('withdraw');
        Route::get('/events/{event}/attendees', [EventController::class, 'attendees'])->name('events.attendees');
        Route::get('/events/{event}/checkin', [EventController::class, 'checkinView'])->name('events.checkin');
        Route::post('/events/{event}/checkin', [EventController::class, 'checkin'])->name('events.checkin.scan');
        Route::resource('events', EventController::class);
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
