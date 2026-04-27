<?php

namespace App\Http\Controllers;

use App\Models\OrganizerWithdrawal;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrganizerController extends Controller
{
    public function pending(Request $request)
    {
        abort_unless($request->user()->role === 'organizer', 403);

        $profile = $request->user()->organizerProfile;
        return view('organizer.pending', compact('profile'));
    }

    public function dashboard(Request $request)
    {
        $user = $request->user();
        $eventsCount = $user->events()->count();
        $publishedCount = $user->events()->where('status', 'published')->count();
        
        $totalTicketsSold = \App\Models\OrderItem::whereHas('ticket.event', function ($query) use ($user) {
            $query->where('organizer_id', $user->id);
        })->whereHas('order', function ($query) {
            $query->where('status', 'paid');
        })->sum('quantity');

        $totalRevenue = \App\Models\OrderItem::whereHas('ticket.event', function ($query) use ($user) {
            $query->where('organizer_id', $user->id);
        })->whereHas('order', function ($query) {
            $query->where('status', 'paid');
        })->sum('subtotal');

        $recentEvents = $user->events()->latest()->take(5)->get();

        return view('organizer.dashboard', compact(
            'eventsCount', 
            'publishedCount', 
            'totalTicketsSold', 
            'totalRevenue',
            'recentEvents'
        ));
    }

    public function balance(Request $request)
    {
        $user = $request->user();

        $eventSales = OrderItem::query()
            ->selectRaw('events.id, events.title, events.banner_path, events.start_time, SUM(order_items.quantity) as tickets_sold, SUM(order_items.subtotal) as revenue')
            ->join('tickets', 'tickets.id', '=', 'order_items.ticket_id')
            ->join('events', 'events.id', '=', 'tickets.event_id')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('events.organizer_id', $user->id)
            ->where('orders.status', 'paid')
            ->groupBy('events.id', 'events.title', 'events.banner_path', 'events.start_time')
            ->orderByDesc('revenue')
            ->get();

        $totalRevenue = (float) $eventSales->sum('revenue');
        $totalTicketsSold = (int) $eventSales->sum('tickets_sold');
        $availableBalance = (float) $user->balance;
        $recentWithdrawals = $user->withdrawals()->latest()->take(5)->get();

        return view('organizer.balance', compact(
            'eventSales',
            'totalRevenue',
            'totalTicketsSold',
            'availableBalance',
            'recentWithdrawals'
        ));
    }

    public function withdraw(Request $request)
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:1'],
        ], [
            'amount.required' => 'Nominal withdraw wajib diisi.',
            'amount.numeric' => 'Nominal withdraw harus berupa angka.',
            'amount.min' => 'Nominal withdraw minimal Rp 1.',
        ]);

        try {
            DB::transaction(function () use ($request, $validated) {
                $organizer = \App\Models\User::query()
                    ->where('id', $request->user()->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $amount = (float) $validated['amount'];
                $currentBalance = (float) $organizer->balance;

                if ($amount > $currentBalance) {
                    throw new \RuntimeException('Saldo tidak mencukupi untuk withdraw.');
                }

                $organizer->decrement('balance', $amount);

                OrganizerWithdrawal::query()->create([
                    'organizer_id' => $organizer->id,
                    'amount' => $amount,
                ]);
            });
        } catch (\RuntimeException $exception) {
            return back()
                ->withErrors(['amount' => $exception->getMessage()])
                ->withInput();
        }

        return back()->with('success', 'Withdraw berhasil diproses.');
    }
}
