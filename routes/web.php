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

Route::get('/kategori/{slug}', function ($slug) {
    // Cari berdasarkan slug, jika tidak ada fallback ke pencarian nama
    $category = \App\Models\EventCategory::where('slug', $slug)->first();

    if (!$category) {
        $category = \App\Models\EventCategory::whereRaw('LOWER(name) LIKE ?', ['%' . strtolower(str_replace('-', ' ', $slug)) . '%'])->first();
    }

    $events = \App\Models\Event::with('tickets', 'organizer')
                ->where('category_id', optional($category)->id)
                ->where('status', 'published')
                ->latest()
                ->paginate(6);
                
    return view('categories.show', compact('category', 'events'));
})->name('category.show');

Route::get('/event/{slug}', function ($slug) {
    $event = \App\Models\Event::with('tickets', 'organizer')->where('slug', $slug)->firstOrFail();
    return view('events.show', [
        'event' => $event,
        'midtransClientKey' => config('services.midtrans.client_key'),
        'midtransSnapJsUrl' => app(\App\Services\MidtransService::class)->getSnapJsUrl(),
        'midtransEnabled' => app(\App\Services\MidtransService::class)->isConfigured(),
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
    });

    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/organizers', [AdminController::class, 'verifyOrganizers'])->name('organizers');
        Route::get('/organizers/{id}', [AdminController::class, 'showOrganizer'])->name('organizers.show');
        Route::post('/organizers/{id}/verify', [AdminController::class, 'approveOrganizer'])->name('organizers.verify');
        Route::post('/organizers/{id}/reject', [AdminController::class, 'rejectOrganizer'])->name('organizers.reject');
        Route::get('/events', [AdminController::class, 'approveEvents'])->name('events');
        Route::post('/events/{id}/approve', [AdminController::class, 'publishEvent'])->name('events.approve');
    });

    Route::middleware('role:organizer')->prefix('organizer')->name('organizer.')->group(function () {
        Route::get('/dashboard', [OrganizerController::class, 'dashboard'])->name('dashboard');
        Route::resource('events', EventController::class);
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
