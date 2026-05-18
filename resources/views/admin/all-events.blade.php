<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
            {{ __('Event Management') }}
        </h2>
    </x-slot>

    @php
        $tabs = [
            'all' => 'All Events',
            'pending_review' => 'Pending Review',
            'draft' => 'Draft',
            'published' => 'Published',
            'rejected' => 'Rejected',
        ];

        $statusClasses = [
            'published' => 'bg-green-100 text-green-700 border-green-200',
            'pending_review' => 'bg-orange-100 text-orange-700 border-orange-200',
            'draft' => 'bg-slate-100 text-slate-700 border-slate-200',
            'rejected' => 'bg-red-100 text-red-700 border-red-200',
        ];

        $sortOptions = [
            'newest_submission' => 'Newest Submission',
            'oldest_submission' => 'Oldest Submission',
            'event_soonest' => 'Event Date Soonest',
            'event_latest' => 'Event Date Latest',
        ];
    @endphp

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <div class="flex flex-col gap-4">
                    <div>
                        <p class="text-xs font-black text-blue-600 uppercase tracking-[0.22em]">Events</p>
                        <h3 class="mt-2 text-2xl font-black text-gray-900">Event Management</h3>
                        <p class="mt-2 text-sm font-medium text-gray-500">
                            Manage all events from one page. Use tabs to open the review queue, drafts, active events, or rejected events.
                        </p>
                    </div>
                </div>

                <div class="mt-6 flex gap-2 overflow-x-auto border-b border-gray-100 pb-3">
                    @foreach($tabs as $tabStatus => $label)
                        @php $isActive = $status === $tabStatus; @endphp
                        <a
                            href="{{ route('admin.events.all', ['status' => $tabStatus, 'sort' => $sort]) }}"
                            class="shrink-0 inline-flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-extrabold transition-colors {{ $isActive ? 'bg-gray-900 text-white shadow-sm' : 'bg-gray-50 text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}"
                        >
                            <span>{{ $label }}</span>
                            <span class="rounded-full px-2 py-0.5 text-[11px] font-black {{ $isActive ? 'bg-white/15 text-white' : 'bg-white text-gray-500 border border-gray-100' }}">
                                {{ $statusCounts[$tabStatus] ?? 0 }}
                            </span>
                        </a>
                    @endforeach
                </div>

                <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-sm font-bold text-gray-500">{{ $statusCounts[$status] ?? 0 }} event records</p>
                    <form method="GET" action="{{ route('admin.events.all') }}" class="flex items-center gap-2">
                        <input type="hidden" name="status" value="{{ $status }}">
                        <select id="event-sort" name="sort" onchange="this.form.submit()" class="rounded-xl border-gray-200 text-sm font-bold text-gray-700 focus:border-blue-500 focus:ring-blue-500">
                            @foreach($sortOptions as $value => $label)
                                <option value="{{ $value }}" @selected($sort === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </form>
                </div>

                @if(session('success'))
                    <div class="mt-6 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm font-bold text-green-700">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="mt-6 overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-100">
                                <th class="p-4 text-xs font-black text-gray-500 uppercase tracking-wider">Info Event</th>
                                <th class="p-4 text-xs font-black text-gray-500 uppercase tracking-wider">Organizer</th>
                                <th class="p-4 text-xs font-black text-gray-500 uppercase tracking-wider text-right">Status</th>
                                <th class="p-4 text-xs font-black text-gray-500 uppercase tracking-wider text-right">Schedule</th>
                                <th class="p-4 text-xs font-black text-gray-500 uppercase tracking-wider text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($events as $event)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="p-4 align-top">
                                        <div class="font-extrabold text-gray-900">{{ $event->title }}</div>
                                        <div class="mt-1 text-sm font-medium text-gray-500">{{ $event->location_name ?: 'Location not provided' }}</div>
                                    </td>
                                    <td class="p-4 align-top text-sm font-bold text-gray-700">
                                        {{ $event->organizer->name ?? 'Organizer unavailable' }}
                                    </td>
                                    <td class="p-4 align-top text-right">
                                        <span class="inline-flex rounded-full border px-3 py-1 text-xs font-black uppercase tracking-wider {{ $statusClasses[$event->status] ?? 'bg-gray-100 text-gray-700 border-gray-200' }}">
                                            {{ $tabs[$event->status] ?? ucfirst(str_replace('_', ' ', $event->status)) }}
                                        </span>
                                    </td>
                                    <td class="p-4 align-top text-right text-sm font-medium text-gray-600">
                                        <div>{{ optional($event->start_time)->format('d M Y, H:i') }}</div>
                                        @if($event->end_time)
                                            <div class="text-xs text-gray-400">{{ $event->end_time->format('d M Y, H:i') }}</div>
                                        @endif
                                    </td>
                                    <td class="p-4 align-top text-right">
                                        <a href="{{ route('admin.events.show', $event->slug) }}" class="inline-flex items-center justify-center rounded-xl bg-gray-900 px-4 py-2 text-sm font-extrabold text-white shadow-sm transition hover:bg-gray-800">
                                            {{ in_array($event->status, ['pending_review', 'draft'], true) ? 'Review' : 'Details' }}
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-10 text-center text-sm font-bold italic text-gray-400">
                                        No events found in this tab.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-6">
                    {{ $events->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
