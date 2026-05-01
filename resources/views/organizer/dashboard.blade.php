<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
            {{ __('Organizer Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            <!-- 1. Hero Banner -->
            @php
                 $status = auth()->user()->organizerProfile?->status ?? 'pending';
                 $isVerified = $status === 'verified';
            @endphp
            <div class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-2xl p-8 shadow-xl text-white flex flex-col md:flex-row items-center justify-between gap-6 relative overflow-hidden">
                <div class="absolute top-0 right-0 -mr-16 -mt-16 text-white/10">
                    <svg class="w-64 h-64" fill="currentColor" viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 9h-2V7h-2v5H6v2h2v5h2v-5h2v-2z"></path></svg>
                </div>
                <div class="relative z-10 w-full md:w-auto">
                    <div class="flex items-center gap-3 mb-2">
                        <h3 class="text-3xl lg:text-4xl font-black">Welcome back, {{ auth()->user()->name }}!</h3>
                    </div>
                    <div class="flex items-center gap-3 mt-3">
                        <span class="text-blue-100 text-lg">Manage your events, view your sales, and grow your audience.</span>
                        <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-widest {{ $isVerified ? 'bg-green-400 text-green-900' : 'bg-yellow-400 text-yellow-900' }}">
                            {{ $isVerified ? 'Verified Organizer' : 'Pending Verification' }}
                        </span>
                    </div>
                </div>
                <div class="relative z-10 w-full md:w-auto mt-4 md:mt-0 whitespace-nowrap">
                    @if($isVerified)
                        <a href="{{ route('organizer.events.create') }}" class="inline-flex items-center justify-center px-6 py-3 bg-white text-indigo-600 font-bold rounded-xl shadow-lg hover:bg-gray-50 transition transform hover:-translate-y-1 w-full md:w-auto">
                            + Create New Event
                        </a>
                    @else
                        <button disabled class="inline-flex opacity-50 items-center justify-center px-6 py-3 bg-white text-gray-500 font-bold rounded-xl shadow cursor-not-allowed w-full md:w-auto" title="You must be verified to create events.">
                            + Create New Event
                        </button>
                    @endif
                </div>
            </div>

            <!-- 2. Stats Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Total Events -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-blue-50 flex items-center justify-center text-blue-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    </div>
                    <div>
                        <p class="text-sm text-slate-500 font-medium">Total Events</p>
                        <h4 class="text-2xl font-black text-slate-900">{{ number_format($eventsCount ?? 0) }}</h4>
                    </div>
                </div>

                <!-- Published Events -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-green-50 flex items-center justify-center text-green-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <div>
                        <p class="text-sm text-slate-500 font-medium">Published Events</p>
                        <h4 class="text-2xl font-black text-slate-900">{{ number_format($publishedCount ?? 0) }}</h4>
                    </div>
                </div>

                <!-- Tickets Sold -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-amber-50 flex items-center justify-center text-amber-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path></svg>
                    </div>
                    <div>
                        <p class="text-sm text-slate-500 font-medium">Tickets Sold</p>
                        <h4 class="text-2xl font-black text-slate-900">{{ number_format($totalTicketsSold ?? 0) }}</h4>
                    </div>
                </div>

                <!-- Total Revenue -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-emerald-50 flex items-center justify-center text-emerald-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <p class="text-sm text-slate-500 font-medium">Total Revenue</p>
                        <h4 class="text-xl lg:text-2xl font-black text-slate-900">Rp {{ number_format($totalRevenue ?? 0, 0, ',', '.') }}</h4>
                    </div>
                </div>
            </div>

            <!-- Main Content Area -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- 3. Recent Events Table -->
                <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                    <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                        <h4 class="text-xl font-bold text-slate-900">Recent Events</h4>
                        <a href="{{ route('organizer.events.index') }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-semibold transition">View All →</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50 border-b border-slate-100">
                                    <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Event Name</th>
                                    <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Date</th>
                                    <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Status</th>
                                    <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($recentEvents ?? [] as $event)
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="p-4">
                                        <p class="font-bold text-slate-900 line-clamp-1" title="{{ $event->title }}">{{ $event->title }}</p>
                                    </td>
                                    <td class="p-4">
                                        <p class="text-sm text-slate-600">{{ $event->start_time->format('d M Y') }}</p>
                                    </td>
                                    <td class="p-4 text-center">
                                        @php
                                            $st = $event->status;
                                            $bg = match($st) {
                                                'published' => 'bg-green-100 text-green-700',
                                                'pending_review' => 'bg-yellow-100 text-yellow-700',
                                                'draft' => 'bg-gray-100 text-gray-700',
                                                default => 'bg-slate-100 text-slate-700'
                                            };
                                        @endphp
                                        <span class="px-2.5 py-1 rounded-md text-[11px] font-bold uppercase tracking-wider {{ $bg }}">
                                            {{ str_replace('_', ' ', $st) }}
                                        </span>
                                    </td>
                                    <td class="p-4 text-right">
                                        <a href="{{ route('organizer.events.attendees', $event->id) }}" class="inline-flex items-center justify-center px-4 py-1.5 bg-indigo-50 border border-indigo-100 text-indigo-600 hover:bg-indigo-600 hover:text-white rounded-lg text-xs font-bold transition-colors">
                                            View Attendees
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="p-8 text-center text-slate-500 italic">No events found. Start organizing your first event!</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- 4. Quick Actions column -->
                <div class="space-y-6">
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
                        <h4 class="text-xl font-bold text-slate-900 mb-4">Quick Actions</h4>
                        <div class="grid grid-cols-1 gap-4">
                            <a href="{{ route('organizer.events.index') }}" class="group flex items-center p-4 border border-slate-200 rounded-xl hover:border-indigo-500 hover:shadow-md transition">
                                <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center group-hover:bg-indigo-600 group-hover:text-white transition mr-4">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                                </div>
                                <div class="flex-1">
                                    <h5 class="font-bold text-slate-900">My Events</h5>
                                    <p class="text-xs text-slate-500">Manage all your events</p>
                                </div>
                                <svg class="w-5 h-5 text-slate-400 group-hover:text-indigo-500 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </a>
                            
                            <a href="{{ route('organizer.events.index') }}" 
                               class="group flex items-center p-4 border border-slate-200 
                               rounded-xl hover:border-emerald-500 hover:shadow-md transition">
                                <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 
                                            flex items-center justify-center mr-4 flex-shrink-0 
                                            group-hover:bg-emerald-500 group-hover:text-white transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" 
                                         viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" 
                                              stroke-width="2" 
                                              d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 3.5V16M4 6h16M4 10h16">
                                        </path>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h5 class="font-bold text-slate-900">Scan QR Check-in</h5>
                                    <p class="text-sm text-slate-500">Pilih event untuk mulai check-in</p>
                                </div>
                                <svg class="w-5 h-5 text-slate-400 group-hover:text-emerald-500 transition flex-shrink-0" 
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" 
                                          stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
