<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrganizerProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
        $failedTransactionsCount = Order::whereIn('status', ['failed', 'cancelled'])->count();
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
            'pendingOrganizers',
            'pendingOrganizersList',
            'pendingEvents',
            'pendingEventsList',
            'draftEvents',
            'draftEventsList'
        ));
    }

    public function users(Request $request)
    {
        $sort = $request->query('sort', 'newest');
        $sorts = ['newest', 'oldest'];

        if (! in_array($sort, $sorts, true)) {
            $sort = 'newest';
        }

        $users = User::where('role', 'attendee')
            ->withCount('orders')
            ->withSum([
                'orders as total_spent' => fn ($query) => $query->where('status', 'paid'),
            ], 'total_amount')
            ->addSelect([
                'paid_tickets_count' => OrderItem::query()
                    ->selectRaw('COALESCE(SUM(order_items.quantity), 0)')
                    ->join('orders', 'orders.id', '=', 'order_items.order_id')
                    ->whereColumn('orders.user_id', 'users.id')
                    ->where('orders.status', 'paid'),
            ])
            ->orderBy('created_at', $sort === 'oldest' ? 'asc' : 'desc')
            ->paginate(10)
            ->appends(['sort' => $sort]);

        return view('admin.users', compact('users', 'sort'));
    }

    public function allOrganizers(Request $request)
    {
        $statuses = ['all', 'verified', 'pending', 'rejected'];
        $status = $request->query('status', 'verified');

        if (! in_array($status, $statuses, true)) {
            $status = 'verified';
        }

        $sort = $request->query('sort', $status === 'pending' ? 'oldest' : 'newest');
        $sorts = ['newest', 'oldest'];

        if (! in_array($sort, $sorts, true)) {
            $sort = $status === 'pending' ? 'oldest' : 'newest';
        }

        $organizers = OrganizerProfile::with('user')
            ->when($status !== 'all', fn ($query) => $query->where('status', $status))
            ->orderBy('created_at', $sort === 'oldest' ? 'asc' : 'desc')
            ->paginate(10)
            ->appends(['status' => $status, 'sort' => $sort]);

        $statusCounts = [
            'all' => OrganizerProfile::count(),
            'verified' => OrganizerProfile::where('status', 'verified')->count(),
            'pending' => OrganizerProfile::where('status', 'pending')->count(),
            'rejected' => OrganizerProfile::where('status', 'rejected')->count(),
        ];

        return view('admin.all-organizers', compact('organizers', 'status', 'statusCounts', 'sort'));
    }

    public function allEvents(Request $request)
    {
        $statuses = ['all', 'pending_review', 'draft', 'published', 'rejected'];
        $status = $request->query('status', 'all');

        if (! in_array($status, $statuses, true)) {
            $status = 'all';
        }

        $sort = $request->query('sort', 'newest_submission');
        $sorts = ['newest_submission', 'oldest_submission', 'event_soonest', 'event_latest'];

        if (! in_array($sort, $sorts, true)) {
            $sort = 'newest_submission';
        }

        $events = Event::with('organizer')
            ->when($status !== 'all', fn ($query) => $query->where('status', $status))
            ->when($sort === 'newest_submission', fn ($query) => $query->orderByDesc('created_at'))
            ->when($sort === 'oldest_submission', fn ($query) => $query->orderBy('created_at'))
            ->when($sort === 'event_soonest', fn ($query) => $query->orderBy('start_time'))
            ->when($sort === 'event_latest', fn ($query) => $query->orderByDesc('start_time'))
            ->paginate(10)
            ->appends(['status' => $status, 'sort' => $sort]);

        $statusCounts = [
            'all' => Event::count(),
            'pending_review' => Event::where('status', 'pending_review')->count(),
            'draft' => Event::where('status', 'draft')->count(),
            'published' => Event::where('status', 'published')->count(),
            'rejected' => Event::where('status', 'rejected')->count(),
        ];

        return view('admin.all-events', compact('events', 'status', 'statusCounts', 'sort'));
    }

    public function draftEvents()
    {
        return redirect()->route('admin.events.all', ['status' => 'draft']);
    }

    public function transactions(Request $request)
    {
        $status = $request->query('status', 'all');
        $statuses = ['all', 'pending', 'paid', 'issues'];

        if (! in_array($status, $statuses, true)) {
            $status = 'all';
        }

        $sort = $request->query('sort', 'newest');
        $sorts = ['newest', 'oldest', 'amount_high', 'amount_low'];

        if (! in_array($sort, $sorts, true)) {
            $sort = 'newest';
        }

        $orders = Order::with(['user', 'orderItems.ticket.event'])
            ->when($status === 'pending', fn ($query) => $query->where('status', 'pending'))
            ->when($status === 'paid', fn ($query) => $query->where('status', 'paid'))
            ->when($status === 'issues', fn ($query) => $query->whereIn('status', ['failed', 'cancelled']))
            ->when($sort === 'newest', fn ($query) => $query->orderByDesc('created_at'))
            ->when($sort === 'oldest', fn ($query) => $query->orderBy('created_at'))
            ->when($sort === 'amount_high', fn ($query) => $query->orderByDesc('total_amount'))
            ->when($sort === 'amount_low', fn ($query) => $query->orderBy('total_amount'))
            ->paginate(10)
            ->appends(['status' => $status, 'sort' => $sort]);

        $statusCounts = [
            'all' => Order::count(),
            'pending' => Order::where('status', 'pending')->count(),
            'paid' => Order::where('status', 'paid')->count(),
            'issues' => Order::whereIn('status', ['failed', 'cancelled'])->count(),
        ];

        return view('admin.transactions', compact('orders', 'status', 'statusCounts', 'sort'));
    }

    public function verifyOrganizers()
    {
        return redirect()->route('admin.organizers.all', ['status' => 'pending']);
    }

    public function showOrganizer($id)
    {
        $organizer = OrganizerProfile::with(['user', 'latestPortfolioReview'])->findOrFail($id);
        return view('admin.organizer-detail', compact('organizer'));
    }

    public function downloadPortfolio($id)
    {
        $organizer = OrganizerProfile::findOrFail($id);

        abort_if(! $organizer->portfolio_path, 404);
        abort_if(! Storage::disk('public')->exists($organizer->portfolio_path), 404);

        $filename = ($organizer->company_name ?? 'portfolio') . '_portfolio.docx';
        $filename = preg_replace('/[^A-Za-z0-9_\-.]/', '_', $filename);

        return Storage::disk('public')->download($organizer->portfolio_path, $filename);
    }

    public function approveOrganizer(Request $request, $id)
    {
        $profile = OrganizerProfile::findOrFail($id);
        $profile->update(['status' => 'verified']);
        $profile->user?->update(['role' => 'organizer']);

        return redirect()
            ->route('admin.organizers.all', ['status' => 'pending'])
            ->with('success', 'Organizer verified successfully.');
    }

    public function rejectOrganizer(Request $request, $id)
    {
        $profile = OrganizerProfile::findOrFail($id);
        $profile->update(['status' => 'rejected']);
        $profile->user?->update(['role' => 'attendee']);

        return redirect()
            ->route('admin.organizers.all', ['status' => 'pending'])
            ->with('success', 'Organizer application rejected.');
    }

    public function approveEvents()
    {
        return redirect()->route('admin.events.all', ['status' => 'pending_review']);
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
            ->with('success', 'Event saved as draft and can be reviewed again later.');
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

    public function deleteEvent(Event $event)
    {
        $title = $event->title;
        $event->delete();

        return redirect()
            ->route('admin.events.all')
            ->with('success', "Event \"{$title}\" has been deleted.");
    }
}
