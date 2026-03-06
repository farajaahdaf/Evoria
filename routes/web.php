<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\OrganizerController;
use App\Http\Controllers\Organizer\EventController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $events = \App\Models\Event::where('status', 'published')->latest()->take(6)->get();
    $categories = \App\Models\EventCategory::all();
    return view('welcome', compact('events', 'categories'));
});

Route::get('/event/{slug}', function ($slug) {
    $event = \App\Models\Event::with('tickets', 'organizer')->where('slug', $slug)->firstOrFail();
    return view('events.show', compact('event'));
})->name('events.show');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function (Illuminate\Http\Request $request) {
        if ($request->user()->role === 'admin') return redirect()->route('admin.dashboard');
        if ($request->user()->role === 'organizer') return redirect()->route('organizer.dashboard');
        return redirect()->route('attendee.dashboard');
    })->name('dashboard');

    Route::middleware('role:attendee')->prefix('attendee')->name('attendee.')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\AttendeeController::class, 'dashboard'])->name('dashboard');
        Route::post('/book/{eventId}', [\App\Http\Controllers\AttendeeController::class, 'bookTicket'])->name('book');
    });

    Route::post('/chat', [\App\Http\Controllers\ChatbotController::class, 'chat'])->name('chat');

    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/organizers', [AdminController::class, 'verifyOrganizers'])->name('organizers');
        Route::post('/organizers/{id}/verify', [AdminController::class, 'approveOrganizer'])->name('organizers.verify');
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
