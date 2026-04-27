<x-app-layout>
    @php
        $reviewStage = match ($event->status) {
            'draft' => 'Draft Review',
            'published' => 'Published',
            'rejected' => 'Rejected',
            default => 'Pending Review',
        };
        $ticketSummary = $event->tickets->sum('quota');
    @endphp

    <div class="min-h-screen bg-slate-50 py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <nav class="flex mb-4 text-xs font-bold tracking-widest text-slate-400 uppercase" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('admin.dashboard') }}" class="hover:text-indigo-600 transition">REGISTRY</a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="w-4 h-4 text-slate-300" fill="currentColor" viewBox="0 0 20 20"><path d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"></path></svg>
                            <a href="{{ route('admin.events') }}" class="ml-1 md:ml-2 hover:text-indigo-600 transition">EVENTS</a>
                        </div>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center text-indigo-600">
                            <svg class="w-4 h-4 text-slate-300" fill="currentColor" viewBox="0 0 20 20"><path d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"></path></svg>
                            <span class="ml-1 md:ml-2">{{ Str::upper($event->title) }}</span>
                        </div>
                    </li>
                </ol>
            </nav>

            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-10 gap-6">
                <div class="max-w-3xl">
                    <h1 class="text-5xl font-extrabold text-slate-900 mb-4">
                        Reviewing <span class="text-indigo-600">{{ Str::upper($event->title) }}</span>
                    </h1>
                    <p class="text-slate-500 text-lg font-medium leading-relaxed">
                        {{ $event->description ?: 'Pending admin review for event publication. Validate schedule, venue details, ticket structure, and supporting documents before publishing.' }}
                    </p>
                </div>

                @if($event->status === 'pending_review')
                    <div class="flex flex-wrap gap-4">
                        <button type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-event-draft')" class="inline-flex items-center px-8 py-4 bg-slate-100 text-slate-700 rounded-xl font-bold hover:bg-slate-200 transition shadow-sm transform hover:-translate-y-0.5">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5h14v14H5z"></path></svg>
                            Save as Draft
                        </button>
                        <button type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-event-rejection')" class="inline-flex items-center px-8 py-4 bg-white border border-red-200 text-red-600 rounded-xl font-bold hover:bg-red-50 transition shadow-sm transform hover:-translate-y-0.5">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            Reject
                        </button>
                        <button type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-event-approval')" class="inline-flex items-center px-8 py-4 bg-indigo-600 text-white rounded-xl font-bold hover:bg-indigo-700 transition shadow-lg shadow-indigo-100 transform hover:-translate-y-0.5">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Approve & Publish
                        </button>
                    </div>
                @elseif($event->status === 'draft')
                    <div class="flex flex-wrap gap-4">
                        <div class="inline-flex items-center px-6 py-4 bg-slate-100 text-slate-700 rounded-xl font-bold border border-slate-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5h14v14H5z"></path></svg>
                            Draft Review Parked
                        </div>
                        <button type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-event-rejection')" class="inline-flex items-center px-8 py-4 bg-white border border-red-200 text-red-600 rounded-xl font-bold hover:bg-red-50 transition shadow-sm transform hover:-translate-y-0.5">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            Reject Event
                        </button>
                        <button type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-event-approval')" class="inline-flex items-center px-8 py-4 bg-indigo-600 text-white rounded-xl font-bold hover:bg-indigo-700 transition shadow-lg shadow-indigo-100 transform hover:-translate-y-0.5">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Approve & Publish
                        </button>
                    </div>
                @endif
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
                <div class="lg:col-span-2 space-y-8">
                    <div class="bg-white rounded-3xl p-8 shadow-sm border border-slate-100">
                        <div class="flex items-center justify-between mb-10">
                            <h3 class="text-xs font-bold tracking-widest text-indigo-500 uppercase">Event Information</h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-y-10 gap-x-8">
                            <div>
                                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Event Name</label>
                                <p class="text-xl font-bold text-slate-800">{{ $event->title }}</p>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Category</label>
                                <p class="text-xl font-bold text-slate-800">{{ $event->category->name ?? 'Uncategorized' }}</p>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Date Initiated</label>
                                <p class="text-xl font-bold text-slate-800">{{ $event->created_at->format('F d, Y') }}</p>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Submission Stage</label>
                                <div class="flex items-center">
                                    <div class="w-2 h-2 rounded-full bg-indigo-500 mr-2"></div>
                                    <p class="text-xl font-bold text-slate-800">{{ $reviewStage }}</p>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
                        <div class="h-80 bg-slate-100 relative">
                            @if($event->banner_path)
                                <img src="{{ Storage::url($event->banner_path) }}" class="w-full h-full object-cover" alt="{{ $event->title }}">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-slate-900">
                                    <span class="text-6xl font-black text-white opacity-10">EVORIA</span>
                                </div>
                            @endif
                            <div class="absolute inset-x-0 bottom-0 p-6">
                                <div class="bg-white/90 backdrop-blur-md p-6 rounded-2xl shadow-xl border border-white/20">
                                    <span class="text-[10px] font-black text-blue-600 uppercase tracking-widest">{{ $event->category->name ?? 'UNCATEGORIZED' }}</span>
                                    <h4 class="text-3xl font-black text-gray-900 leading-tight mt-1">{{ $event->title }}</h4>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-4 text-sm text-gray-600 font-bold">
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-1.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                            {{ $event->start_time->format('M d, Y H:i') }}
                                        </div>
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-1.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                            {{ $event->location_name ?: 'Venue pending' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="p-8 space-y-8">
                            <div>
                                <h3 class="text-xs font-bold tracking-widest text-indigo-500 uppercase mb-4">Event Brief</h3>
                                <div class="text-slate-600 leading-relaxed font-medium space-y-4">
                                    {!! nl2br(e($event->description)) !!}
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-6 border-t border-slate-100">
                                <div class="rounded-2xl bg-slate-50 p-5">
                                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Venue</label>
                                    <p class="text-lg font-bold text-slate-800">{{ $event->location_name ?: 'Not provided' }}</p>
                                    <p class="text-sm text-slate-500 mt-2">{{ $event->address ?: 'No detailed address submitted yet.' }}</p>
                                </div>
                                <div class="rounded-2xl bg-slate-50 p-5">
                                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Schedule Window</label>
                                    <p class="text-lg font-bold text-slate-800">{{ $event->start_time->format('d M Y H:i') }}</p>
                                    <p class="text-sm text-slate-500 mt-2">Ends {{ $event->end_time->format('d M Y H:i') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="space-y-8">
                    <div class="bg-white rounded-3xl p-8 shadow-sm border border-slate-100 flex flex-col items-center text-center">
                        <div class="flex items-center justify-between w-full mb-10">
                            <h3 class="text-xs font-bold tracking-widest text-indigo-500 uppercase">Lead Organizer</h3>
                        </div>

                        <div class="relative mb-6">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($event->organizer->name) }}&background=0F172A&color=fff&size=128" alt="Avatar" class="w-32 h-32 rounded-3xl object-cover shadow-xl border-4 border-white">
                            <div class="absolute -bottom-2 -right-2 bg-indigo-600 p-2 rounded-xl text-white shadow-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 1.343-3 3v7h6v-7c0-1.657-1.343-3-3-3z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 20h14"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8V4"></path></svg>
                            </div>
                        </div>

                        <h4 class="text-2xl font-bold text-slate-900 mb-1">{{ $event->organizer->name }}</h4>
                        <p class="text-slate-400 font-medium mb-8">Registered Organizer</p>

                        <div class="w-full space-y-3 mb-8">
                            <div class="bg-slate-50 rounded-xl p-3 flex justify-between items-center">
                                <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">EMAIL</span>
                                <span class="text-xs font-bold text-indigo-600">{{ $event->organizer->email }}</span>
                            </div>
                            <div class="bg-slate-50 rounded-xl p-3 flex justify-between items-center">
                                <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">STATUS</span>
                                <span class="text-xs font-bold text-slate-700">{{ ucfirst(optional($event->organizer->organizerProfile)->status ?? 'unknown') }}</span>
                            </div>
                        </div>

                        @if(optional($event->organizer->organizerProfile)->id)
                            <a href="{{ route('admin.organizers.show', $event->organizer->organizerProfile->id) }}" class="w-full py-4 bg-slate-50 text-slate-900 font-extrabold rounded-2xl flex items-center justify-center space-x-2 hover:bg-slate-100 transition-all border border-slate-100 group">
                                <span>View Organizer Profile</span>
                                <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                <div class="bg-white rounded-3xl p-8 shadow-sm border border-slate-100">
                    <div class="flex items-center justify-between mb-10">
                        <h3 class="text-xs font-bold tracking-widest text-indigo-500 uppercase">Event Entity</h3>
                        <svg class="w-6 h-6 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>

                    <div class="flex items-start space-x-6 mb-8">
                        <div class="w-16 h-16 bg-slate-900 rounded-2xl flex items-center justify-center text-white text-2xl font-black shadow-lg">
                            {{ Str::upper(Str::substr($event->title, 0, 1)) }}
                        </div>
                        <div>
                            <h4 class="text-2xl font-bold text-slate-900">{{ $event->title }}</h4>
                            <p class="text-slate-400 font-medium">{{ $event->category->name ?? 'Uncategorized Event' }}</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="bg-slate-50 rounded-2xl p-6">
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Audience Capacity</label>
                            <div class="flex items-center text-slate-700 font-bold">
                                <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5V4H2v16h5m10 0v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5m10 0H7"></path></svg>
                                {{ number_format($ticketSummary) }} total seats across {{ $event->tickets->count() }} ticket tier{{ $event->tickets->count() === 1 ? '' : 's' }}
                            </div>
                        </div>
                        <div class="bg-slate-50 rounded-2xl p-6">
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Ticket Strategy</label>
                            <p class="text-slate-700 font-bold">{{ $event->tickets->max('price') > 0 ? 'Paid admission configured' : 'Free admission only' }}</p>
                            <p class="text-sm text-slate-500 mt-2">Price range IDR {{ number_format((float) $event->tickets->min('price'), 0, ',', '.') }} - {{ number_format((float) $event->tickets->max('price'), 0, ',', '.') }}</p>
                        </div>
                    </div>
                </div>

                <div id="documents" class="bg-white rounded-3xl p-8 shadow-sm border border-slate-100">
                    <div class="flex items-center justify-between mb-10">
                        <h3 class="text-xs font-bold tracking-widest text-indigo-500 uppercase">Supporting Documents</h3>
                    </div>

                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-5 bg-slate-50 rounded-2xl border border-transparent hover:border-indigo-100 transition group">
                            <div class="flex items-center">
                                <div class="w-12 h-12 bg-indigo-50 rounded-xl flex items-center justify-center mr-4 group-hover:bg-indigo-100 transition">
                                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                </div>
                                <div>
                                    <h5 class="font-bold text-slate-800">Event Portfolio</h5>
                                    <p class="text-xs text-slate-400 font-medium">Supporting visual or PDF portfolio</p>
                                </div>
                            </div>
                            @if($event->portfolio_path)
                                <a href="{{ Storage::url($event->portfolio_path) }}" target="_blank" class="p-2 text-slate-400 hover:text-indigo-600 transition">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                </a>
                            @else
                                <span class="text-xs font-bold text-red-400 italic">Missing</span>
                            @endif
                        </div>

                        <div class="flex items-center justify-between p-5 bg-slate-50 rounded-2xl border border-transparent hover:border-indigo-100 transition group">
                            <div class="flex items-center">
                                <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center mr-4 group-hover:bg-blue-100 transition">
                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                </div>
                                <div>
                                    <h5 class="font-bold text-slate-800">Event Proposal</h5>
                                    <p class="text-xs text-slate-400 font-medium">Formal event proposal document</p>
                                </div>
                            </div>
                            @if($event->proposal_path)
                                <a href="{{ Storage::url($event->proposal_path) }}" target="_blank" class="p-2 text-slate-400 hover:text-indigo-600 transition">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                </a>
                            @else
                                <span class="text-xs font-bold text-red-400 italic">Missing</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-3xl p-8 shadow-sm border border-slate-100 mb-8">
                <div class="flex items-center justify-between mb-10">
                    <h3 class="text-xs font-bold tracking-widest text-indigo-500 uppercase">Ticket Matrix</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                    @forelse($event->tickets as $ticket)
                        <div class="rounded-2xl border border-slate-100 bg-slate-50 p-5">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">{{ $ticket->name }}</p>
                            <p class="text-2xl font-black text-slate-900">IDR {{ number_format($ticket->price, 0, ',', '.') }}</p>
                            <div class="mt-4 flex items-center justify-between text-sm font-bold text-slate-500">
                                <span>Quota</span>
                                <span class="text-slate-900">{{ $ticket->quota }}</span>
                            </div>
                            <div class="mt-2 flex items-center justify-between text-sm font-bold text-slate-500">
                                <span>Available</span>
                                <span class="text-slate-900">{{ $ticket->available_qty }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="md:col-span-2 xl:col-span-3 p-8 text-center text-slate-500 italic bg-slate-50 rounded-2xl">No ticket data available.</div>
                    @endforelse
                </div>
            </div>

            <div class="bg-white rounded-3xl p-8 shadow-sm border border-slate-100 mb-8">
                <div class="flex items-center justify-between mb-10">
                    <h3 class="text-xs font-bold tracking-widest text-indigo-500 uppercase">Process Log</h3>
                </div>

                <div class="space-y-10 relative">
                    <div class="absolute left-1.5 top-2 bottom-2 w-0.5 bg-slate-100"></div>
                    <div class="relative flex items-start space-x-6">
                        <div class="w-3.5 h-3.5 rounded-full bg-indigo-600 ring-4 ring-indigo-50 z-10 mt-1.5"></div>
                        <div class="flex-1">
                            <div class="flex justify-between items-center mb-1">
                                <h5 class="font-bold text-slate-800 text-lg">Event Submitted</h5>
                                <span class="text-sm font-bold text-slate-400 tabular-nums">{{ $event->created_at->format('H:i:s') }}</span>
                            </div>
                            <p class="text-slate-500 font-medium">Organizer "{{ $event->organizer->name }}" submitted "{{ $event->title }}" for publication review.</p>
                        </div>
                    </div>
                    <div class="relative flex items-start space-x-6">
                        <div class="w-3.5 h-3.5 rounded-full {{ $event->status === 'published' ? 'bg-emerald-500 ring-4 ring-emerald-50' : ($event->status === 'rejected' ? 'bg-red-500 ring-4 ring-red-50' : ($event->status === 'draft' ? 'bg-slate-500 ring-4 ring-slate-100' : 'bg-slate-300')) }} z-10 mt-1.5"></div>
                        <div class="flex-1">
                            <div class="flex justify-between items-center mb-1 {{ $event->status === 'pending_review' ? 'text-slate-400' : 'text-slate-700' }}">
                                <h5 class="font-bold text-lg">Moderation Decision</h5>
                                <span class="text-sm font-bold tabular-nums">{{ $event->status === 'pending_review' ? '-- : -- : --' : $event->updated_at->format('H:i:s') }}</span>
                            </div>
                            <p class="{{ $event->status === 'pending_review' ? 'text-slate-400 italic' : 'text-slate-500 font-medium' }}">
                                @if($event->status === 'draft')
                                    Event has been moved to draft by admin and is still waiting for a final accept or reject decision.
                                @elseif($event->status === 'published')
                                    Event has been accepted by admin and published for attendee access.
                                @elseif($event->status === 'rejected')
                                    Event submission has been rejected and returned to the organizer.
                                @else
                                    Pending admin manual verification of event details and uploaded documents.
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-modal name="confirm-event-rejection" focusable>
        <div class="p-8">
            <div class="flex items-center justify-center w-16 h-16 mx-auto mb-6 bg-red-100 rounded-2xl">
                <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
            </div>

            <h2 class="text-2xl font-bold text-center text-slate-900 mb-2">
                Reject Event Submission?
            </h2>

            <p class="text-center text-slate-500 font-medium mb-10 leading-relaxed">
                Are you sure you want to reject <strong>"{{ $event->title }}"</strong>? This action will return the event to the organizer workflow.
            </p>

            <div class="flex flex-col sm:flex-row gap-4">
                <button type="button" x-on:click="$dispatch('close')" class="flex-1 px-6 py-4 bg-slate-100 text-slate-600 rounded-2xl font-bold hover:bg-slate-200 transition">
                    Cancel, Keep Reviewing
                </button>

                <form action="{{ route('admin.events.reject', $event->slug) }}" method="POST" class="flex-1">
                    @csrf
                    <button type="submit" class="w-full px-6 py-4 bg-red-600 text-white rounded-2xl font-bold hover:bg-red-700 transition shadow-lg shadow-red-100">
                        Yes, Reject Event
                    </button>
                </form>
            </div>
        </div>
    </x-modal>

    <x-modal name="confirm-event-draft" focusable>
        <div class="p-8">
            <div class="flex items-center justify-center w-16 h-16 mx-auto mb-6 bg-slate-100 rounded-2xl">
                <svg class="w-8 h-8 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5h14v14H5z"></path>
                </svg>
            </div>

            <h2 class="text-2xl font-bold text-center text-slate-900 mb-2">
                Save Event as Draft?
            </h2>

            <p class="text-center text-slate-500 font-medium mb-10 leading-relaxed">
                <strong>"{{ $event->title }}"</strong> will be moved to draft so admin can review it again later without approving or rejecting it yet.
            </p>

            <div class="flex flex-col sm:flex-row gap-4">
                <button type="button" x-on:click="$dispatch('close')" class="flex-1 px-6 py-4 bg-slate-100 text-slate-600 rounded-2xl font-bold hover:bg-slate-200 transition">
                    Cancel
                </button>

                <form action="{{ route('admin.events.draft', $event->slug) }}" method="POST" class="flex-1">
                    @csrf
                    <button type="submit" class="w-full px-6 py-4 bg-slate-700 text-white rounded-2xl font-bold hover:bg-slate-800 transition shadow-lg shadow-slate-100">
                        Yes, Save as Draft
                    </button>
                </form>
            </div>
        </div>
    </x-modal>

    <x-modal name="confirm-event-approval" focusable>
        <div class="p-8">
            <div class="flex items-center justify-center w-16 h-16 mx-auto mb-6 bg-green-100 rounded-2xl">
                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>

            <h2 class="text-2xl font-bold text-center text-slate-900 mb-2">
                Approve & Publish Event?
            </h2>

            <p class="text-center text-slate-500 font-medium mb-10 leading-relaxed">
                Are you sure you want to approve <strong>"{{ $event->title }}"</strong>? Once approved, the event will be published immediately for attendees.
            </p>

            <div class="flex flex-col sm:flex-row gap-4">
                <button type="button" x-on:click="$dispatch('close')" class="flex-1 px-6 py-4 bg-slate-100 text-slate-600 rounded-2xl font-bold hover:bg-slate-200 transition">
                    Cancel, Review Again
                </button>

                <form action="{{ route('admin.events.approve', $event->slug) }}" method="POST" class="flex-1">
                    @csrf
                    <button type="submit" class="w-full px-6 py-4 bg-indigo-600 text-white rounded-2xl font-bold hover:bg-indigo-700 transition shadow-lg shadow-indigo-100">
                        Yes, Approve & Publish
                    </button>
                </form>
            </div>
        </div>
    </x-modal>
</x-app-layout>
