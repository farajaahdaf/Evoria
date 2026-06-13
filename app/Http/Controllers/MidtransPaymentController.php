<?php

namespace App\Http\Controllers;

use App\Jobs\GenerateETicketsJob;
use App\Models\Order;
use App\Services\MidtransService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MidtransPaymentController extends Controller
{
    public function notification(Request $request, MidtransService $midtrans): JsonResponse
    {
        $payload = $request->all();

        if (! $midtrans->verifyNotificationSignature($payload)) {
            return response()->json(['message' => 'Invalid signature.'], 403);
        }

        $this->syncOrderFromPayload($payload, $midtrans);

        return response()->json(['message' => 'Notification processed.']);
    }

    public function syncOrder(Order $order, MidtransService $midtrans): Order
    {
        $payload = $midtrans->getTransactionStatus($order->order_number);
        $this->syncOrderFromPayload($payload, $midtrans);

        return $order->fresh(['orderItems.ticket.event', 'orderItems.eTickets']);
    }

    protected function syncOrderFromPayload(array $payload, MidtransService $midtrans): void
    {
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
                // Synchronous: e-ticket harus sudah ada saat syncOrder() mengembalikan
                // order ke mobile, kalau tidak QR code belum ke-generate di response
                // pertama. Job idempotent (missing = quantity - existing), aman dijalankan
                // ulang oleh webhook Midtrans.
                GenerateETicketsJob::dispatchSync($order->id);
                $this->creditOrganizerBalance($order);
            }
        });
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

    protected function creditOrganizerBalance(Order $order): void
    {
        $order->loadMissing('orderItems.ticket.event');

        $creditsByOrganizer = [];

        foreach ($order->orderItems as $item) {
            $organizerId = $item->ticket?->event?->organizer_id;

            if (! $organizerId) {
                continue;
            }

            $creditsByOrganizer[$organizerId] = ($creditsByOrganizer[$organizerId] ?? 0) + (float) $item->subtotal;
        }

        if (empty($creditsByOrganizer)) {
            return;
        }

        $organizers = \App\Models\User::query()
            ->whereIn('id', array_keys($creditsByOrganizer))
            ->lockForUpdate()
            ->get()
            ->keyBy('id');

        foreach ($creditsByOrganizer as $organizerId => $creditAmount) {
            $organizer = $organizers->get($organizerId);

            if (! $organizer) {
                continue;
            }

            $organizer->increment('balance', $creditAmount);
        }
    }
}
