<?php

namespace App\Http\Controllers;

use App\Jobs\GenerateETicketsJob;
use App\Models\Order;
use App\Models\Ticket;
use App\Services\MidtransService;
use App\Services\WaitingRoomService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class AttendeeController extends Controller
{
    public function dashboard(Request $request)
    {
        $orders = $request->user()
            ->orders()
            ->with(['orderItems.ticket.event', 'orderItems.eTickets'])
            ->latest()
            ->get();

        return view('attendee.tickets', [
            'orders' => $orders,
            'midtransClientKey' => config('services.midtrans.client_key'),
            'midtransSnapJsUrl' => app(MidtransService::class)->getSnapJsUrl(),
            'midtransEnabled' => app(MidtransService::class)->isConfigured(),
        ]);
    }

    public function bookTicket(Request $request, $eventId, MidtransService $midtrans, WaitingRoomService $waitingRoom)
    {
        if (! $waitingRoom->isAdmitted((int) $eventId, $request->user()->id)) {
            return response()->json([
                'message' => 'Sesi antrian Anda belum aktif atau sudah berakhir. Silakan masuk antrian lagi.',
            ], 423);
        }

        $request->validate([
            'ticket_id' => ['required', 'exists:tickets,id'],
            'quantity' => ['required', 'integer', 'min:1', 'max:5'],
        ]);

        $quantity = (int) $request->integer('quantity');
        $ticketId = (int) $request->integer('ticket_id');

        $order = null;
        $ticket = null;

        try {
            DB::transaction(function () use ($request, $eventId, $ticketId, $quantity, &$order, &$ticket) {
                $ticket = Ticket::query()
                    ->with('event')
                    ->lockForUpdate()
                    ->findOrFail($ticketId);

                if ((int) $ticket->event_id !== (int) $eventId) {
                    throw new RuntimeException('Tiket tidak cocok dengan event yang dipilih.');
                }

                if ($ticket->event?->status !== 'published') {
                    throw new RuntimeException('Event ini belum tersedia untuk pembayaran.');
                }

                if ($ticket->available_qty < $quantity) {
                    throw new RuntimeException('Jumlah tiket yang tersedia tidak mencukupi.');
                }

                $order = $request->user()->orders()->create([
                    'order_number' => 'ORD-' . now()->format('YmdHis') . '-' . Str::upper(Str::random(6)),
                    'total_amount' => (float) $ticket->price * $quantity,
                    'status' => 'pending',
                    'payment_method' => 'midtrans',
                ]);

                $order->orderItems()->create([
                    'ticket_id' => $ticket->id,
                    'quantity' => $quantity,
                    'price' => $ticket->price,
                    'subtotal' => (float) $ticket->price * $quantity,
                ]);

                $ticket->decrement('available_qty', $quantity);
            });

            if ((float) $order->total_amount <= 0) {
                $order->update([
                    'status' => 'paid',
                    'payment_method' => 'free',
                ]);

                GenerateETicketsJob::dispatchSync($order->id);

                $waitingRoom->releaseSlot((int) $eventId, $request->user()->id);

                return response()->json([
                    'message' => 'Tiket gratis berhasil dipesan.',
                    'order_number' => $order->order_number,
                    'snap_token' => null,
                    'redirect_url' => route('attendee.dashboard'),
                ]);
            }

            if (! $midtrans->isConfigured()) {
                throw new RuntimeException('Midtrans belum dikonfigurasi. Isi MIDTRANS_CLIENT_KEY dan MIDTRANS_SERVER_KEY di file .env.');
            }

            $snapResponse = $midtrans->createSnapTransaction($order);

            $order->update([
                'snap_token' => $snapResponse['token'] ?? null,
            ]);

            $waitingRoom->releaseSlot((int) $eventId, $request->user()->id);

            return response()->json([
                'message' => 'Transaksi berhasil dibuat.',
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'snap_token' => $order->snap_token,
                'redirect_url' => $snapResponse['redirect_url'] ?? null,
            ]);
        } catch (\Throwable $exception) {
            if ($order instanceof Order) {
                DB::transaction(function () use ($order) {
                    $order->loadMissing('orderItems.ticket');

                    foreach ($order->orderItems as $item) {
                        if ($item->ticket) {
                            $item->ticket()->increment('available_qty', $item->quantity);
                        }
                    }

                    $order->delete();
                });
            }

            return response()->json([
                'message' => $exception instanceof RuntimeException
                    ? $exception->getMessage()
                    : 'Gagal membuat transaksi Midtrans.',
            ], 422);
        }
    }

    public function refreshOrderStatus(Request $request, Order $order, MidtransService $midtrans, MidtransPaymentController $paymentController): JsonResponse
    {
        abort_unless($order->user_id === $request->user()->id, 403);

        if ($order->status === 'paid') {
            return response()->json([
                'message' => 'Status order sudah sinkron.',
                'status' => $order->status,
            ]);
        }

        $order = $paymentController->syncOrder($order, $midtrans);

        // Jika sudah paid, pastikan e-tickets sudah ada sebelum response dikirim
        // sehingga QR langsung muncul tanpa perlu refresh manual.
        if ($order->status === 'paid') {
            GenerateETicketsJob::dispatchSync($order->id);
        }

        return response()->json([
            'message' => 'Status order berhasil disinkronkan.',
            'status' => $order->status,
        ]);
    }
}
