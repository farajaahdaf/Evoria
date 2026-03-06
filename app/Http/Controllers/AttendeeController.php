<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AttendeeController extends Controller
{
    public function dashboard(Request $request)
    {
        $orders = $request->user()->orders()->with('orderItems.ticket.event')->latest()->get();
        return view('attendee.dashboard', compact('orders'));
    }

    public function bookTicket(Request $request, $eventId)
    {
        $request->validate([
            'ticket_id' => 'required|exists:tickets,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $ticket = \App\Models\Ticket::findOrFail($request->ticket_id);
        
        if ($ticket->available_qty < $request->quantity) {
            return back()->with('error', 'Not enough tickets available!');
        }

        // Extremely simplified simulation of creating an order. Actual flow would redirect to payment gateway (Midtrans).
        $order = $request->user()->orders()->create([
            'order_number' => 'ORD-' . strtoupper(uniqid()),
            'total_amount' => $ticket->price * $request->quantity,
            'status' => 'paid', // SIMULATED AS PAID DIRECTLY FOR DEMO
            'payment_method' => 'simulation'
        ]);

        $orderItem = $order->orderItems()->create([
            'ticket_id' => $ticket->id,
            'quantity' => $request->quantity,
            'price' => $ticket->price,
            'subtotal' => $ticket->price * $request->quantity
        ]);

        $ticket->decrement('available_qty', $request->quantity);

        // Simulate e-ticket generation
        for($i=0; $i<$request->quantity; $i++) {
            $orderItem->eTickets()->create([
                'ticket_code' => 'TCKT-' . strtoupper(uniqid())
            ]);
        }

        return redirect()->route('attendee.dashboard')->with('success', 'Tickets booked successfully! (Simulated Payment)');
    }
}
