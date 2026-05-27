<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\GenerateETicketsJob;
use App\Models\Order;
use App\Models\Ticket;
use App\Services\MidtransService;
use App\Services\WaitingRoomService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class BookingController extends Controller
{
    public function book(Request $request, int $eventId, MidtransService $midtrans, WaitingRoomService $waitingRoom): JsonResponse
    {
        if (! $waitingRoom->isAdmitted($eventId, $request->user()->id)) {
            return response()->json([
                'message' => 'Sesi antrian Anda belum aktif atau sudah berakhir. Silakan masuk antrian lagi.',
            ], 423);
        }

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
                GenerateETicketsJob::dispatchSync($order->id);

                $waitingRoom->releaseSlot($eventId, $request->user()->id);

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

            $waitingRoom->releaseSlot($eventId, $request->user()->id);

            $pendingMinutes = (int) config('waitingroom.pending_timeout_minutes', 30);

            return response()->json([
                'message'            => 'Transaksi berhasil dibuat.',
                'order_id'           => $order->id,
                'order_number'       => $order->order_number,
                'snap_token'         => $order->snap_token,
                'redirect_url'       => $snapResponse['redirect_url'] ?? null,
                'is_free'            => false,
                'payment_expires_at' => now()->addMinutes($pendingMinutes)->toIso8601String(),
                'payment_timeout_minutes' => $pendingMinutes,
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
