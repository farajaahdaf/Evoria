<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ChatbotApiController;
use App\Http\Controllers\MidtransPaymentController;

Route::prefix('v1')->group(function () {
    Route::post('/payments/midtrans/notification', [MidtransPaymentController::class, 'notification'])
        ->name('payments.midtrans.notification');

    // Auth Routes
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login', [AuthController::class, 'login']);

    // Public Data Routes
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/events', [EventController::class, 'index']);
    Route::get('/events/{id}', [EventController::class, 'show']);

    // Protected Routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::get('/profile', [AuthController::class, 'profile']);
        Route::post('/profile', [AuthController::class, 'updateProfile']);
        Route::put('/profile/password', [AuthController::class, 'updatePassword']);
        Route::delete('/profile', [AuthController::class, 'destroyAccount']);

        // Booking
        Route::post('/events/{eventId}/book', [BookingController::class, 'book']);

        // Orders & E-Tickets
        Route::get('/orders', [OrderController::class, 'index']);
        Route::get('/orders/{order}', [OrderController::class, 'show']);
        Route::post('/orders/{order}/sync', [OrderController::class, 'syncStatus']);
        Route::post('/orders/{order}/cancel', [OrderController::class, 'cancelOrder']);

        // Chatbot AI
        Route::post('/chatbot', [ChatbotApiController::class, 'chat']);
    });
});
