<x-app-layout>
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
                    @if($event->banner_path)
                        <img src="{{ asset('storage/' . $event->banner_path) }}" class="w-full h-full object-cover">
                    @else
                        <div class="absolute inset-0 bg-gradient-to-tr from-indigo-500 to-purple-500 flex items-center justify-center text-white font-bold text-3xl opacity-80">
                            {{ $event->title }}
                        </div>
                    @endif
                    <div class="absolute top-4 right-4 px-4 py-2 rounded-full text-sm font-bold uppercase tracking-wide
                        {{ $event->status === 'published' ? 'bg-green-100 text-green-800' : 
                           ($event->status === 'pending_review' ? 'bg-yellow-100 text-yellow-800' : 
                           'bg-gray-100 text-gray-800') }}">
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
