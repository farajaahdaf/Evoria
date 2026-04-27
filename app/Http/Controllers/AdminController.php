<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Order;
use App\Models\OrganizerProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalAttendees = User::where('role', 'attendee')->count();
        $totalOrganizers = OrganizerProfile::where('status', 'verified')->count();
        $totalEvents = Event::count();

        $totalTransactions = Order::count();
        $pendingTransactionsCount = Order::where('status', 'pending')->count();
        $paidTransactionsCount = Order::where('status', 'paid')->count();
        $failedTransactionsCount = Order::whereIn('status', ['failed', 'cancelled', 'refunded'])->count();
        $transactionStats = Order::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();

        $pendingOrganizers = OrganizerProfile::where('status', 'pending')->count();
        $pendingOrganizersList = OrganizerProfile::with('user')
            ->where('status', 'pending')
            ->latest()
            ->take(2)
            ->get();

        $pendingEvents = Event::where('status', 'pending_review')->count();
        $pendingEventsList = Event::with('organizer')
            ->where('status', 'pending_review')
            ->latest()
            ->take(1)
            ->get();

        $draftEvents = Event::where('status', 'draft')->count();
        $draftEventsList = Event::with('organizer')
            ->where('status', 'draft')
            ->latest()
            ->take(1)
            ->get();

        return view('admin.dashboard', compact(
            'totalAttendees',
            'totalOrganizers',
            'totalEvents',
            'totalTransactions',
            'pendingTransactionsCount',
            'paidTransactionsCount',
            'failedTransactionsCount',
            'transactionStats',
            'pendingOrganizers',
            'pendingOrganizersList',
            'pendingEvents',
            'pendingEventsList',
            'draftEvents',
            'draftEventsList'
        ));
    }

    public function users()
    {
        $users = User::where('role', 'attendee')->latest()->paginate(10);
        return view('admin.users', compact('users'));
    }

    public function allOrganizers()
    {
        $organizers = OrganizerProfile::with('user')
            ->where('status', 'verified')
            ->latest()
            ->paginate(10);

        return view('admin.all-organizers', compact('organizers'));
    }

    public function allEvents()
    {
        $events = Event::with('organizer')->latest()->paginate(10);
        return view('admin.all-events', compact('events'));
    }

    public function draftEvents()
    {
        $events = Event::with('organizer')
            ->where('status', 'draft')
            ->latest()
            ->paginate(10);

        return view('admin.draft-events', compact('events'));
    }

    public function transactionsOverview()
    {
        $pendingTransactionsCount = Order::where('status', 'pending')->count();
        $paidTransactionsCount = Order::where('status', 'paid')->count();
        $failedTransactionsCount = Order::whereIn('status', ['failed', 'cancelled', 'refunded'])->count();
        $totalTransactions = Order::count();
        $latestOrders = Order::with('user')->latest()->take(6)->get();

        return view('admin.transactions-overview', compact(
            'pendingTransactionsCount',
            'paidTransactionsCount',
            'failedTransactionsCount',
            'totalTransactions',
            'latestOrders'
        ));
    }

    public function transactions()
    {
        $orders = Order::with(['user', 'orderItems.ticket.event'])->latest()->paginate(10);
        return view('admin.transactions', compact('orders'));
    }

    public function verifyOrganizers()
    {
        $organizers = OrganizerProfile::with('user')->where('status', 'pending')->get();
        return view('admin.organizers', compact('organizers'));
    }

    public function showOrganizer($id)
    {
        $organizer = OrganizerProfile::with('user')->findOrFail($id);
        return view('admin.organizer-detail', compact('organizer'));
    }

    public function approveOrganizer(Request $request, $id)
    {
        $profile = OrganizerProfile::findOrFail($id);
        $profile->update(['status' => 'verified']);
        $profile->user?->update(['role' => 'organizer']);

        return redirect()->route('admin.organizers')->with('success', 'Organizer verified successfully.');
    }

    public function rejectOrganizer(Request $request, $id)
    {
        $profile = OrganizerProfile::findOrFail($id);
        $profile->update(['status' => 'rejected']);
        $profile->user?->update(['role' => 'attendee']);

        return redirect()->route('admin.organizers')->with('success', 'Organizer application rejected.');
    }

    public function approveEvents()
    {
        $events = Event::with('organizer')->where('status', 'pending_review')->get();
        return view('admin.events', compact('events'));
    }

    public function showEvent(Event $event)
    {
        $event->load(['organizer.organizerProfile', 'category', 'tickets']);
        return view('admin.event-detail', compact('event'));
    }

    public function saveEventAsDraft(Request $request, Event $event)
    {
        $event->update(['status' => 'draft']);

        return redirect()
            ->route('admin.events.show', $event->slug)
            ->with('success', 'Event disimpan sebagai draft dan dapat ditinjau kembali nanti.');
    }

    public function approveEvent(Request $request, Event $event)
    {
        $event->update(['status' => 'published']);

        return redirect()
            ->route('admin.events.show', $event->slug)
            ->with('success', 'Event approved and published successfully.');
    }

    public function rejectEvent(Request $request, Event $event)
    {
        $event->update(['status' => 'rejected']);

        return redirect()
            ->route('admin.dashboard')
            ->with('success', 'Event application has been rejected.');
    }
}
