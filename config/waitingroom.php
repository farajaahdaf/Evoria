<?php

return [
    // Master switch. When false, the queue gate is bypassed entirely.
    'enabled' => env('WAITINGROOM_ENABLED', true),

    // How many users may hold a checkout slot at the same time (per event).
    'max_active' => (int) env('WAITINGROOM_MAX_ACTIVE', 20),

    // Max users admitted from the queue per second (smooths the thundering herd).
    'admit_rate' => (int) env('WAITINGROOM_ADMIT_RATE', 10),

    // How long an admitted user keeps their slot before it auto-expires (seconds).
    'hold_seconds' => (int) env('WAITINGROOM_HOLD_SECONDS', 600),

    // Redis connection name (defaults to the app default).
    'connection' => env('WAITINGROOM_REDIS_CONNECTION', 'default'),

    // Safety net: cancel pending orders older than this and return their stock.
    // Covers users who got a slot, started checkout, but never paid (and where
    // Midtrans never delivered an expire notification).
    'pending_timeout_minutes' => (int) env('BOOKING_PENDING_TIMEOUT_MINUTES', 30),
];
