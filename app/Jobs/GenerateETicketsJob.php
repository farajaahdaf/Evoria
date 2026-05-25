<?php

namespace App\Jobs;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class GenerateETicketsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $orderId)
    {
    }

    public function handle(): void
    {
        $order = Order::query()
            ->with('orderItems.eTickets')
            ->find($this->orderId);

        if (! $order || $order->status !== 'paid') {
            return;
        }

        foreach ($order->orderItems as $item) {
            $missing = max($item->quantity - $item->eTickets->count(), 0);

            for ($i = 0; $i < $missing; $i++) {
                $item->eTickets()->create([
                    'ticket_code' => 'TCKT-' . Str::upper(Str::random(12)),
                    'status' => 'issued',
                ]);
            }
        }
    }
}
