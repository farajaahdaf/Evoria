<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
            {{ __('Admin Control Panel') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-700 px-6 py-4 rounded-2xl shadow-sm flex items-center space-x-3">
                    <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span class="font-bold">{{ session('success') }}</span>
                </div>
            @endif

            <div class="relative bg-gray-900 rounded-3xl overflow-hidden shadow-2xl">
                <div class="absolute inset-0 bg-gradient-to-r from-blue-900/50 to-transparent"></div>
                <div class="relative p-10 flex items-center justify-between">
                    <div class="max-w-lg space-y-4">
                        <p class="text-xs font-black text-blue-200 uppercase tracking-[0.25em]">Moderation Center</p>
                        <h3 class="text-4xl font-extrabold text-white tracking-tight">Admin Overview</h3>
                        <p class="text-gray-300 text-lg leading-relaxed">
                            There are currently {{ $pendingOrganizers }} pending organizers, {{ $pendingEvents }} events awaiting review, {{ $draftEvents }} draft events, and {{ $pendingTransactionsCount }} pending transactions. Prioritize queues that still need an admin decision.
                        </p>
                        @if($pendingOrganizers > 0 || $pendingEvents > 0)
                            <div class="flex flex-wrap gap-4 pt-4">
                                @if($pendingOrganizers > 0)
                                    <a href="{{ route('admin.organizers.all', ['status' => 'pending']) }}" class="px-6 py-3 bg-white text-gray-900 font-bold rounded-xl hover:bg-gray-100 transition-all shadow-lg">Review Organizers</a>
                                @endif
                                @if($pendingEvents > 0)
                                    <a href="{{ route('admin.events.all', ['status' => 'pending_review']) }}" class="px-6 py-3 bg-gray-800 text-white font-bold rounded-xl border border-gray-700 hover:bg-gray-700 transition-all shadow-lg">Review Events</a>
                                @endif
                            </div>
                        @endif
                    </div>
                    <div class="hidden md:flex pr-10 opacity-90 items-center justify-center">
                        <img src="{{ asset('images/logo.png') }}" alt="Evoria" class="w-56 h-auto object-contain drop-shadow-[0_18px_40px_rgba(15,23,42,0.25)]">
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 relative overflow-hidden">
                    <div class="flex justify-between items-start mb-4">
                        <div class="p-3 bg-gray-50 rounded-2xl text-gray-900">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        </div>
                    </div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Attendee</p>
                    <h4 class="text-3xl font-black text-gray-900">{{ number_format($totalAttendees) }}</h4>
                </div>

                <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 relative overflow-hidden">
                    <div class="flex justify-between items-start mb-4">
                        <div class="p-3 bg-gray-50 rounded-2xl text-gray-900">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        </div>
                    </div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Organizer</p>
                    <h4 class="text-3xl font-black text-gray-900">{{ number_format($totalOrganizers) }}</h4>
                </div>

                <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 relative overflow-hidden">
                    <div class="flex justify-between items-start mb-4">
                        <div class="p-3 bg-gray-50 rounded-2xl text-gray-900">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                    </div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Total Event</p>
                    <h4 class="text-3xl font-black text-gray-900">{{ number_format($totalEvents) }}</h4>
                </div>

                <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 relative overflow-hidden">
                    <div class="flex justify-between items-start mb-4">
                        <div class="p-3 bg-gray-50 rounded-2xl text-gray-900">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        </div>
                    </div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Transactions</p>
                    <h4 class="text-3xl font-black text-gray-900">{{ number_format($totalTransactions) }}</h4>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                <div class="lg:col-span-8 space-y-8">
                    <div class="space-y-6">
                        <div class="flex justify-between items-center">
                            <h4 class="text-2xl font-black text-gray-900 tracking-tight">Pending Organizers</h4>
                            <a href="{{ route('admin.organizers.all', ['status' => 'pending']) }}" class="text-sm font-bold text-gray-500 hover:text-blue-600 transition-colors">View All</a>
                        </div>

                        <div class="space-y-4">
                            @forelse($pendingOrganizersList as $org)
                                <div class="bg-white p-5 rounded-3xl shadow-sm border border-gray-50 flex items-center justify-between group hover:shadow-md transition-all duration-300">
                                    <div class="flex items-center space-x-5">
                                        <div class="w-14 h-14 bg-gray-100 rounded-2xl flex items-center justify-center text-gray-400 group-hover:bg-blue-50 transition-colors">
                                            <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 3c1.93 0 3.5 1.57 3.5 3.5S13.93 13 12 13s-3.5-1.57-3.5-3.5S10.07 6 12 6zm7 13H5v-.5c0-2.33 4.67-3.5 7-3.5s7 1.17 7 3.5v.5z"/></svg>
                                        </div>
                                        <div>
                                            <h5 class="text-lg font-extrabold text-gray-900 leading-tight">{{ $org->company_name }}</h5>
                                            <p class="text-xs text-gray-400 font-bold mt-1 uppercase tracking-wider">Applied: {{ $org->created_at->format('M d, Y') }} • {{ $org->user->name }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-4">
                                        <span class="px-3 py-1 bg-purple-50 text-purple-600 text-[10px] font-black uppercase tracking-widest rounded-full border border-purple-100">New Application</span>
                                        <a href="{{ route('admin.organizers.show', $org->id) }}" class="px-5 py-2.5 bg-gray-900 text-white text-sm font-bold rounded-xl hover:bg-gray-800 transition-all shadow-md">Review</a>
                                    </div>
                                </div>
                            @empty
                                <div class="p-10 bg-gray-50 rounded-3xl border-2 border-dashed border-gray-200 text-center text-gray-400 font-bold italic">
                                    No pending applications in the moderation queue.
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div class="flex justify-between items-center">
                            <h4 class="text-2xl font-black text-gray-900 tracking-tight">Pending Events</h4>
                            <a href="{{ route('admin.events.all', ['status' => 'pending_review']) }}" class="text-sm font-bold text-gray-500 hover:text-blue-600 transition-colors">View All</a>
                        </div>

                        <div class="space-y-4">
                            @forelse($pendingEventsList as $event)
                                <div class="bg-white rounded-3xl shadow-sm border border-gray-50 overflow-hidden flex flex-col md:flex-row group hover:shadow-xl transition-all duration-500">
                                    <div class="md:w-72 h-64 relative bg-gray-100 overflow-hidden">
                                        @if($event->banner_path)
                                            <img src="{{ Storage::url($event->banner_path) }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" alt="{{ $event->title }}">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center bg-gray-900 text-white">
                                                <span class="font-black text-4xl opacity-20 uppercase tracking-tighter">EVORIA</span>
                                            </div>
                                        @endif
                                        <div class="absolute inset-0 bg-gradient-to-t from-gray-900/80 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                                    </div>
                                    <div class="p-8 flex flex-col justify-between flex-1">
                                        <div class="space-y-3">
                                            <div class="flex items-center justify-between">
                                                <span class="text-[10px] font-black text-blue-600 uppercase tracking-widest">Large Scale Event</span>
                                                <span class="text-xs font-bold text-gray-400 flex items-center">
                                                    <svg class="w-3.5 h-3.5 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/></svg>
                                                    {{ $event->created_at->diffForHumans() }}
                                                </span>
                                            </div>
                                            <h5 class="text-2xl font-black text-gray-900 leading-tight">{{ $event->title }}</h5>
                                            <p class="text-gray-500 text-sm line-clamp-2 leading-relaxed font-medium">
                                                {{ Str::limit($event->description, 120) }}
                                            </p>
                                        </div>
                                        <div class="flex items-center justify-end pt-6 border-t border-gray-50 mt-6">
                                            <a href="{{ route('admin.events.show', $event->slug) }}" class="px-6 py-2.5 bg-gray-900 text-white text-sm font-extrabold rounded-xl hover:bg-gray-800 transition-all shadow-lg">Review</a>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="p-10 bg-gray-50 rounded-3xl border-2 border-dashed border-gray-200 text-center text-gray-400 font-bold italic">
                                    No events pending approval. Queue is clear!
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div class="flex justify-between items-center">
                            <div class="flex items-center gap-3">
                                <h4 class="text-2xl font-black text-gray-900 tracking-tight">Draft Events</h4>
                                <span class="px-3 py-1 bg-slate-100 text-slate-600 text-[10px] font-black uppercase tracking-widest rounded-full border border-slate-200">{{ $draftEvents }}</span>
                            </div>
                            <a href="{{ route('admin.events.all', ['status' => 'draft']) }}" class="text-sm font-bold text-gray-500 hover:text-blue-600 transition-colors">View All</a>
                        </div>

                        <div class="space-y-4">
                            @forelse($draftEventsList as $event)
                                <div class="bg-white rounded-3xl shadow-sm border border-gray-50 overflow-hidden flex flex-col md:flex-row group hover:shadow-xl transition-all duration-500">
                                    <div class="md:w-72 h-64 relative bg-gray-100 overflow-hidden">
                                        @if($event->banner_path)
                                            <img src="{{ Storage::url($event->banner_path) }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" alt="{{ $event->title }}">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center bg-slate-800 text-white">
                                                <span class="font-black text-4xl opacity-20 uppercase tracking-tighter">DRAFT</span>
                                            </div>
                                        @endif
                                        <div class="absolute inset-0 bg-gradient-to-t from-slate-900/70 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                                    </div>
                                    <div class="p-8 flex flex-col justify-between flex-1">
                                        <div class="space-y-3">
                                            <div class="flex items-center justify-between">
                                                <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Review Parked</span>
                                                <span class="text-xs font-bold text-gray-400 flex items-center">
                                                    <svg class="w-3.5 h-3.5 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/></svg>
                                                    {{ $event->updated_at->diffForHumans() }}
                                                </span>
                                            </div>
                                            <h5 class="text-2xl font-black text-gray-900 leading-tight">{{ $event->title }}</h5>
                                            <p class="text-gray-500 text-sm line-clamp-2 leading-relaxed font-medium">
                                                {{ Str::limit($event->description, 120) }}
                                            </p>
                                            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">
                                                {{ $event->organizer->name }} • {{ $event->start_time->format('M d, Y H:i') }}
                                            </p>
                                        </div>
                                        <div class="flex items-center justify-between pt-6 border-t border-gray-50 mt-6">
                                            <span class="px-3 py-1 bg-slate-100 text-slate-700 text-[10px] font-black uppercase tracking-widest rounded-full border border-slate-200">Draft</span>
                                            <a href="{{ route('admin.events.show', $event->slug) }}" class="px-6 py-2.5 bg-gray-900 text-white text-sm font-extrabold rounded-xl hover:bg-gray-800 transition-all shadow-lg">Review Again</a>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="p-10 bg-gray-50 rounded-3xl border-2 border-dashed border-gray-200 text-center text-gray-400 font-bold italic">
                                    No draft events parked by admin at the moment.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-4">
                    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 sticky top-10 space-y-10">
                        <div class="flex items-center space-x-4">
                            <div class="p-3 bg-purple-50 rounded-2xl text-purple-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                            </div>
                            <h4 class="text-2xl font-black text-gray-900 tracking-tight">Transaction Status</h4>
                        </div>

                        <div class="space-y-6">
                            <div>
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Pending Transactions</p>
                                <div class="flex items-baseline justify-between">
                                    <h5 class="text-4xl font-black text-gray-900">{{ $pendingTransactionsCount }}</h5>
                                </div>
                                <div class="mt-4 h-2 w-full bg-gray-100 rounded-full overflow-hidden">
                                    <div class="h-full bg-purple-600 rounded-full" style="width: {{ $pendingTransactionsCount > 0 ? min(($pendingTransactionsCount / max($totalTransactions, 1)) * 100, 100) : 0 }}%"></div>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-6">
                            <h6 class="text-xs font-black text-gray-900 uppercase tracking-widest">Transaction Snapshot</h6>
                            <div class="space-y-4">
                                <div class="flex items-center justify-between group">
                                    <span class="text-sm font-bold text-gray-500 group-hover:text-gray-900 transition-colors">Pending</span>
                                    <span class="text-sm font-black text-gray-900">{{ $pendingTransactionsCount }}</span>
                                </div>
                                <div class="flex items-center justify-between group">
                                    <span class="text-sm font-bold text-gray-500 group-hover:text-gray-900 transition-colors">Paid</span>
                                    <span class="text-sm font-black text-emerald-600">{{ $paidTransactionsCount }}</span>
                                </div>
                                <div class="flex items-center justify-between group">
                                    <span class="text-sm font-bold text-gray-500 group-hover:text-gray-900 transition-colors">Failed / Cancelled</span>
                                    <span class="text-sm font-black text-red-600">{{ $failedTransactionsCount }}</span>
                                </div>
                            </div>
                        </div>

                        <a href="{{ route('admin.transactions') }}" class="w-full py-4 bg-gray-50 text-gray-900 font-extrabold rounded-2xl flex items-center justify-center space-x-2 hover:bg-gray-100 transition-all border border-gray-100 group">
                            <svg class="w-4 h-4 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                            <span>Manage Transactions</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
