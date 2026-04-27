<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\OrganizerController;
use App\Http\Controllers\OrganizerApplicationController;
use App\Http\Controllers\Organizer\EventController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $events = \App\Models\Event::where('status', 'published')->latest()->take(6)->get();
    $categories = \App\Models\EventCategory::all();
    return view('welcome', compact('events', 'categories'));
})->name('home');

Route::get('/kategori/{slug}', function (\Illuminate\Http\Request $request, $slug) {
    // Cari berdasarkan slug, jika tidak ada fallback ke pencarian nama
    $category = \App\Models\EventCategory::where('slug', $slug)->first();

    if (!$category) {
        $category = \App\Models\EventCategory::whereRaw('LOWER(name) LIKE ?', ['%' . strtolower(str_replace('-', ' ', $slug)) . '%'])->first();
    }

    $sort = $request->query('sort', 'latest'); // latest | price_asc | price_desc

    $query = \App\Models\Event::with('tickets', 'organizer')
                ->where('category_id', optional($category)->id)
                ->where('status', 'published');

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

    $events = $query->paginate(6)->appends(['sort' => $sort]);
                
    return view('categories.show', compact('category', 'events', 'sort'));
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
    });

    Route::middleware('role:organizer')->prefix('organizer')->name('organizer.')->group(function () {
        Route::get('/dashboard', [OrganizerController::class, 'dashboard'])->name('dashboard');
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
