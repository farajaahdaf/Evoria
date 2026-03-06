<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
            {{ __('My Events') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <div class="flex items-center justify-between">
                <h3 class="text-2xl font-bold text-gray-800">Your Hosted Events</h3>
                <a href="{{ route('organizer.events.create') }}" class="px-5 py-2.5 bg-indigo-600 text-white font-bold rounded-lg shadow-md hover:bg-indigo-700 transition">
                    + Create Event
                </a>
            </div>

            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-sm" role="alert">
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($events as $event)
                <div class="bg-white rounded-2xl overflow-hidden shadow-sm border border-gray-100 flex flex-col hover:shadow-lg transition duration-300">
                    <div class="h-40 bg-gray-200 relative">
                        <!-- Banner Image -->
                        @if($event->banner_path)
                            <img src="{{ asset('storage/' . $event->banner_path) }}" alt="{{ $event->title }}" class="w-full h-full object-cover">
                        @else
                            <div class="absolute inset-0 bg-gradient-to-tr from-indigo-500 to-purple-400 flex items-center justify-center text-white font-bold text-xl opacity-80">
                                {{ substr($event->title, 0, 20) }}...
                            </div>
                        @endif
                        <div class="absolute top-3 right-3 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide
                            {{ $event->status === 'published' ? 'bg-green-100 text-green-800' : 
                               ($event->status === 'pending_review' ? 'bg-yellow-100 text-yellow-800' : 
                               'bg-gray-100 text-gray-800') }}">
                            {{ str_replace('_', ' ', $event->status) }}
                        </div>
                    </div>
                    
                    <div class="p-5 flex-grow flex flex-col">
                        <h4 class="text-xl font-bold text-gray-900 mb-2 truncate" title="{{ $event->title }}">{{ $event->title }}</h4>
                        
                        <div class="text-gray-500 text-sm mb-4 space-y-1">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                {{ $event->start_time->format('d M Y, H:i') }}
                            </div>
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                {{ Str::limit($event->location_name, 25) }}
                            </div>
                        </div>

                        <div class="mt-auto flex space-x-2 pt-4 border-t border-gray-100">
                            <a href="{{ route('organizer.events.show', $event->id) }}" class="flex-1 text-center py-2 bg-gray-50 text-gray-700 font-medium rounded hover:bg-gray-100 transition">View</a>
                            
                            @if($event->status === 'draft')
                            <form action="{{ route('organizer.events.update', $event->id) }}" method="POST" class="flex-1">
                                @csrf @method('PUT')
                                <input type="hidden" name="action" value="submit">
                                <button type="submit" class="w-full text-center py-2 bg-indigo-50 text-indigo-700 font-medium rounded hover:bg-indigo-100 transition">Submit Review</button>
                            </form>
                            @endif
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-span-1 md:col-span-3 bg-white p-12 text-center rounded-2xl shadow-sm">
                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    </div>
                    <h4 class="text-xl font-bold text-gray-800 mb-2">You haven't created any events</h4>
                    <p class="text-gray-500 mb-6">Start managing your first offline event right now.</p>
                    <a href="{{ route('organizer.events.create') }}" class="px-6 py-3 bg-indigo-600 text-white font-bold rounded-lg shadow-md hover:bg-indigo-700 transition inline-block">
                        Create Your First Event
                    </a>
                </div>
                @endforelse
            </div>

            <div class="mt-6">
                {{ $events->links() }}
            </div>

        </div>
    </div>
</x-app-layout>
