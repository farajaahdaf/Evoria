<?php

return [
    // Cancel pending orders older than this and return their stock.
    // Default 1440 menit (24 jam) — sinkron dengan default expiry Midtrans
    // untuk metode pembayaran bank transfer (VA).
    'pending_timeout_minutes' => (int) env('BOOKING_PENDING_TIMEOUT_MINUTES', 1440),
];
