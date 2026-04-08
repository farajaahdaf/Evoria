<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\MidtransService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MidtransPaymentController extends Controller
{
    public function notification(Request $request, MidtransService $midtrans): JsonResponse
    {
        $payload = $request->all();

        if (! $midtrans->verifyNotificationSignature($payload)) {
            return response()->json(['message' => 'Invalid signature.'], 403);
        }

        $mappedStatus = $midtrans->mapTransactionStatus($payload);
        $paymentType = $payload['payment_type'] ?? 'midtrans';

        DB::transaction(function () use ($payload, $mappedStatus, $paymentType) {
            $order = Order::query()
                ->where('order_number', $payload['order_id'] ?? null)
                ->lockForUpdate()
                ->firstOrFail();

            $previousStatus = $order->status;

            $order->update([
                'status' => $mappedStatus,
                'payment_method' => $paymentType,
            ]);

            if ($this->shouldReleaseInventory($previousStatus, $mappedStatus)) {
                $this->restoreReservedTickets($order);
                $this->cancelGeneratedTickets($order);
            }

            if ($mappedStatus === 'paid' && $previousStatus !== 'paid') {
                $this->generateETickets($order);
            }
        });

        return response()->json(['message' => 'Notification processed.']);
    }

    protected function shouldReleaseInventory(string $previousStatus, string $nextStatus): bool
    {
        return ! in_array($previousStatus, ['cancelled', 'failed', 'refunded'], true)
            && in_array($nextStatus, ['cancelled', 'failed', 'refunded'], true);
    }

    protected function restoreReservedTickets(Order $order): void
    {
        $order->loadMissing('orderItems.ticket');

        foreach ($order->orderItems as $item) {
            if ($item->ticket) {
                $item->ticket()->increment('available_qty', $item->quantity);
            }
        }
    }

    protected function cancelGeneratedTickets(Order $order): void
    {
        $order->loadMissing('orderItems.eTickets');

        foreach ($order->orderItems as $item) {
            $item->eTickets()->update([
                'status' => 'cancelled',
            ]);
        }
    }

    protected function generateETickets(Order $order): void
    {
        $order->loadMissing('orderItems.eTickets');

        foreach ($order->orderItems as $item) {
            $existingCount = $item->eTickets->count();
            $missingCount = max($item->quantity - $existingCount, 0);

            for ($i = 0; $i < $missingCount; $i++) {
                $item->eTickets()->create([
                    'ticket_code' => 'TCKT-' . Str::upper(Str::random(12)),
                ]);
            }
        }
    }
}
