<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ExpireStalePendingOrders extends Command
{
    protected $signature = 'orders:expire-stale';

    protected $description = 'Cancel pending orders past the payment timeout and return their reserved stock';

    public function handle(): int
    {
        $minutes = max(5, (int) config('booking.pending_timeout_minutes', 1440));
        $cutoff = now()->subMinutes($minutes);

        $staleOrderIds = Order::query()
            ->where('status', 'pending')
            ->where('created_at', '<', $cutoff)
            ->pluck('id');

        if ($staleOrderIds->isEmpty()) {
            $this->info('No stale pending orders.');

            return self::SUCCESS;
        }

        $released = 0;

        foreach ($staleOrderIds as $orderId) {
            DB::transaction(function () use ($orderId, &$released) {
                $order = Order::query()
                    ->with('orderItems.ticket')
                    ->lockForUpdate()
                    ->find($orderId);

                if (! $order || $order->status !== 'pending') {
                    return;
                }

                foreach ($order->orderItems as $item) {
                    $item->ticket?->increment('available_qty', $item->quantity);
                }

                $order->update(['status' => 'cancelled']);
                $released++;
            });
        }

        $this->info("Cancelled {$released} stale pending order(s) and restored stock.");

        return self::SUCCESS;
    }
}
