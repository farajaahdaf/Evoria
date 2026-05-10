<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Ticket;
use App\Services\MidtransService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class BookingController extends Controller
{
    public function book(Request $request, int $eventId, MidtransService $midtrans): JsonResponse
    {
        $request->validate([
            'ticket_id' => ['required', 'exists:tickets,id'],
            'quantity'  => ['required', 'integer', 'min:1', 'max:5'],
        ]);

        $quantity = (int) $request->integer('quantity');
        $ticketId = (int) $request->integer('ticket_id');

        $order  = null;
        $ticket = null;

        try {
            DB::transaction(function () use ($request, $eventId, $ticketId, $quantity, &$order, &$ticket) {
                $ticket = Ticket::query()
                    ->with('event')
                    ->lockForUpdate()
                    ->findOrFail($ticketId);

                if ((int) $ticket->event_id !== $eventId) {
                    throw new RuntimeException('Tiket tidak cocok dengan event yang dipilih.');
                }

                if ($ticket->event?->status !== 'published') {
                    throw new RuntimeException('Event ini belum tersedia untuk pembelian.');
                }

                if ($ticket->available_qty < $quantity) {
                    throw new RuntimeException('Jumlah tiket yang tersedia tidak mencukupi.');
                }

                $order = $request->user()->orders()->create([
                    'order_number'   => 'ORD-' . now()->format('YmdHis') . '-' . Str::upper(Str::random(6)),
                    'total_amount'   => (float) $ticket->price * $quantity,
                    'status'         => 'pending',
                    'payment_method' => 'midtrans',
                ]);

                $order->orderItems()->create([
                    'ticket_id' => $ticket->id,
                    'quantity'  => $quantity,
                    'price'     => $ticket->price,
                    'subtotal'  => (float) $ticket->price * $quantity,
                ]);

                $ticket->decrement('available_qty', $quantity);
            });

            // Free ticket: mark paid immediately and generate e-tickets
            if ((float) $order->total_amount <= 0) {
                $order->update(['status' => 'paid', 'payment_method' => 'free']);
                $order->loadMissing('orderItems.eTickets');

                foreach ($order->orderItems as $item) {
                    $missing = max($item->quantity - $item->eTickets->count(), 0);
                    for ($i = 0; $i < $missing; $i++) {
                        $item->eTickets()->create([
                            'ticket_code' => 'TCKT-' . Str::upper(Str::random(12)),
                        ]);
                    }
                }

                return response()->json([
                    'message'      => 'Tiket gratis berhasil dipesan.',
                    'order_number' => $order->order_number,
                    'snap_token'   => null,
                    'redirect_url' => null,
                    'is_free'      => true,
                ]);
            }

            if (! $midtrans->isConfigured()) {
                throw new RuntimeException('Pembayaran belum dikonfigurasi di server.');
            }

            $snapResponse = $midtrans->createSnapTransaction($order);

            $order->update(['snap_token' => $snapResponse['token'] ?? null]);

            return response()->json([
                'message'      => 'Transaksi berhasil dibuat.',
                'order_id'     => $order->id,
                'order_number' => $order->order_number,
                'snap_token'   => $order->snap_token,
                'redirect_url' => $snapResponse['redirect_url'] ?? null,
                'is_free'      => false,
            ]);
        } catch (\Throwable $e) {
            if ($order instanceof Order) {
                DB::transaction(function () use ($order) {
                    $order->loadMissing('orderItems.ticket');
                    foreach ($order->orderItems as $item) {
                        $item->ticket?->increment('available_qty', $item->quantity);
                    }
                    $order->delete();
                });
            }

            return response()->json([
                'message' => $e instanceof RuntimeException
                    ? $e->getMessage()
                    : 'Gagal membuat transaksi.',
            ], 422);
        }
    }
}
