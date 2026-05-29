<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\MidtransPaymentController;
use App\Models\Order;
use App\Services\MidtransService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $orders = $request->user()
            ->orders()
            ->with(['orderItems.ticket.event.category', 'orderItems.eTickets'])
            ->latest()
            ->paginate(20);

        return response()->json([
            'status' => 'success',
            'data'   => $orders,
        ]);
    }

    public function show(Request $request, Order $order): JsonResponse
    {
        abort_unless($order->user_id === $request->user()->id, 403);

        $order->load(['orderItems.ticket.event.category', 'orderItems.eTickets']);

        return response()->json([
            'status' => 'success',
            'data'   => $order,
        ]);
    }

    public function syncStatus(
        Request $request,
        Order $order,
        MidtransService $midtrans,
        MidtransPaymentController $paymentController
    ): JsonResponse {
        abort_unless($order->user_id === $request->user()->id, 403);

        if ($order->status === 'paid') {
            return response()->json([
                'status'  => 'success',
                'message' => 'Status order sudah sinkron.',
                'data'    => $order->load(['orderItems.ticket.event', 'orderItems.eTickets']),
            ]);
        }

        $order = $paymentController->syncOrder($order, $midtrans);

        return response()->json([
            'status'  => 'success',
            'message' => 'Status order berhasil disinkronkan.',
            'data'    => $order,
        ]);
    }

    /**
     * Cancel a pending order and immediately return the reserved stock.
     * Called by the mobile app when the user closes Snap without paying
     * (onClose / onError callback) and the order was never confirmed.
     */
    public function cancelOrder(Request $request, Order $order): JsonResponse
    {
        abort_unless($order->user_id === $request->user()->id, 403);

        // Only pending orders can be cancelled this way.
        if ($order->status !== 'pending') {
            return response()->json([
                'status'  => 'error',
                'message' => 'Hanya order dengan status pending yang bisa dibatalkan.',
            ], 422);
        }

        DB::transaction(function () use ($order) {
            $order->loadMissing('orderItems.ticket');

            foreach ($order->orderItems as $item) {
                $item->ticket?->increment('available_qty', $item->quantity);
            }

            $order->update(['status' => 'cancelled']);
        });

        return response()->json([
            'status'  => 'success',
            'message' => 'Order berhasil dibatalkan dan stok tiket dikembalikan.',
        ]);
    }
}
