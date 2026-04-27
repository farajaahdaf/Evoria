<x-app-layout>
    @php
        $bannerUrl = null;

        if (filled($event->banner_path)) {
            if (\Illuminate\Support\Str::startsWith($event->banner_path, ['http://', 'https://'])) {
                $bannerUrl = $event->banner_path;
            } else {
                $normalizedPath = ltrim(preg_replace('#^/?storage/#', '', $event->banner_path), '/');
                $bannerUrl = asset('storage/' . $normalizedPath);
            }
        }
    @endphp

    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
            {{ __('Event Details: ') }} {{ $event->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <div class="bg-white rounded-2xl overflow-hidden shadow-sm border border-gray-100">
                <!-- Banner -->
                <div class="h-64 bg-gray-200 relative">
                    @if($bannerUrl)
                        <img src="{{ $bannerUrl }}" alt="{{ $event->title }}" class="w-full h-full object-cover">
                    @else
                        <div class="absolute inset-0 bg-gradient-to-tr from-indigo-500 to-purple-500 flex items-center justify-center text-white font-bold text-3xl opacity-80">
                            {{ $event->title }}
                        </div>
                    @endif
                    <div class="absolute top-4 right-4 px-4 py-2 rounded-full text-sm font-bold uppercase tracking-wide
                        {{ $event->status === 'published' ? 'bg-green-100 text-green-800' :
                           ($event->status === 'pending_review' ? 'bg-yellow-100 text-yellow-800' :
                           ($event->status === 'rejected' ? 'bg-red-100 text-red-800' :
                           'bg-gray-100 text-gray-800')) }}">
                        Status: {{ str_replace('_', ' ', $event->status) }}
                    </div>
                </div>
                
                <div class="p-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 mb-4 border-b pb-2">Information</h3>
                            <dl class="space-y-3 text-sm">
                                <div class="grid grid-cols-3"><dt class="text-gray-500 font-medium">Category</dt><dd class="col-span-2 font-bold">{{ $event->category->name }}</dd></div>
                                <div class="grid grid-cols-3"><dt class="text-gray-500 font-medium">Schedule</dt><dd class="col-span-2">{{ $event->start_time->format('d M Y, H:i') }} - {{ $event->end_time->format('d M Y, H:i') }}</dd></div>
                                <div class="grid grid-cols-3"><dt class="text-gray-500 font-medium">Venue</dt><dd class="col-span-2 font-bold">{{ $event->location_name }}</dd></div>
                                <div class="grid grid-cols-3"><dt class="text-gray-500 font-medium">Address</dt><dd class="col-span-2">{{ $event->address }}</dd></div>
                            </dl>
                            
                            <h3 class="text-xl font-bold text-gray-900 mt-6 mb-4 border-b pb-2">Documents</h3>
                            <div class="space-y-3">
                                <div class="flex items-center justify-between p-3 bg-blue-50/50 border border-blue-100 rounded-xl">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-blue-100 text-primary rounded-lg flex items-center justify-center">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        </div>
                                        <div>
                                            <div class="text-sm font-bold text-gray-900">Portofolio</div>
                                            <div class="text-xs text-gray-500">Supporting documentation</div>
                                        </div>
                                    </div>
                                    @if($event->portfolio_path)
                                        <a href="{{ asset('storage/' . $event->portfolio_path) }}" target="_blank" class="px-4 py-1.5 bg-white border border-blue-200 text-primary text-xs font-bold rounded-lg hover:bg-blue-50 transition">View File</a>
                                    @else
                                        <span class="text-xs text-gray-400 italic">No file uploaded</span>
                                    @endif
                                </div>

                                <div class="flex items-center justify-between p-3 bg-emerald-50/50 border border-emerald-100 rounded-xl">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-emerald-100 text-emerald-600 rounded-lg flex items-center justify-center">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        </div>
                                        <div>
                                            <div class="text-sm font-bold text-gray-900">Event Proposal</div>
                                            <div class="text-xs text-gray-500">Detailed event plan</div>
                                        </div>
                                    </div>
                                    @if($event->proposal_path)
                                        <a href="{{ asset('storage/' . $event->proposal_path) }}" target="_blank" class="px-4 py-1.5 bg-white border border-emerald-200 text-emerald-600 text-xs font-bold rounded-lg hover:bg-emerald-50 transition">View File</a>
                                    @else
                                        <span class="text-xs text-gray-400 italic">No file uploaded</span>
                                    @endif
                                </div>
                            </div>
                            
                            <h3 class="text-xl font-bold text-gray-900 mt-6 mb-4 border-b pb-2">Description</h3>
                            <div class="prose text-sm text-gray-600">
                                {{ $event->description }}
                            </div>
                        </div>

                        <div>
                            <h3 class="text-xl font-bold text-gray-900 mb-4 border-b pb-2">Ticket Quotas</h3>
                            <div class="space-y-3">
                                @forelse($event->tickets as $ticket)
                                    <div class="flex justify-between items-center p-3 bg-gray-50 border rounded-lg">
                                        <div>
                                            <div class="font-bold text-gray-900">{{ $ticket->name }}</div>
                                            <div class="text-xs text-gray-500">Price: Rp {{ number_format($ticket->price, 0, ',', '.') }}</div>
                                        </div>
                                        <div class="text-right">
                                            <div class="font-bold {{ $ticket->available_qty > 0 ? 'text-green-600' : 'text-red-500' }}">
                                                {{ $ticket->available_qty }} left
                                            </div>
                                            <div class="text-xs text-gray-500">of {{ $ticket->quota }} total</div>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-sm text-gray-500">No tickets configured.</p>
                                @endforelse
                            </div>

                            <div class="mt-8 space-y-3">
                                @if($event->status === 'draft')
                                    <form action="{{ route('organizer.events.update', $event->id) }}" method="POST">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="action" value="submit">
                                        <button type="submit" class="w-full py-3 bg-indigo-600 text-white font-bold rounded-lg hover:bg-indigo-700 transition">Submit to Admin for Review</button>
                                    </form>
                                @elseif($event->status === 'approved')
                                    <!-- In a real app we might have a publish button, or admin auto-publishes. Currently logic uses 'published' -->
                                @endif
                                <a href="{{ route('organizer.events.index') }}" class="block w-full text-center py-3 bg-white border border-gray-300 text-gray-700 font-bold rounded-lg hover:bg-gray-50 transition">Back to Events</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
