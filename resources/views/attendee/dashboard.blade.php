<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
            {{ __('My Tickets') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-sm" role="alert">
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            <h3 class="text-2xl font-bold text-gray-800 mb-6">Upcoming Events</h3>

            <div class="space-y-6">
                @forelse($orders as $order)
                    @foreach($order->orderItems as $item)
                        <div class="bg-white rounded-2xl overflow-hidden shadow-sm border border-gray-100 flex flex-col md:flex-row hover:shadow-lg transition duration-300">
                            <!-- Event Banner Area -->
                            <div class="w-full md:w-1/3 bg-gray-200 relative h-48 md:h-auto">
                                @if($item->ticket->event->banner_path)
                                    <img src="{{ asset('storage/' . $item->ticket->event->banner_path) }}" class="w-full h-full object-cover">
                                @else
                                    <div class="absolute inset-0 bg-gradient-to-tr from-blue-500 to-indigo-600 flex items-center justify-center text-white font-bold opacity-80 p-4 text-center">
                                        {{ $item->ticket->event->title }}
                                    </div>
                                @endif
                                <div class="absolute top-4 left-4 bg-white text-gray-900 text-sm font-bold px-3 py-1 rounded-md shadow-md">
                                    {{ $item->ticket->name }}
                                </div>
                            </div>
                            
                            <!-- Ticket Info Area -->
                            <div class="p-6 md:p-8 flex-1 flex flex-col justify-between">
                                <div>
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="text-indigo-600 font-bold text-sm uppercase tracking-wide">
                                            {{ $item->ticket->event->start_time->format('D, d M Y | H:i') }}
                                        </div>
                                        <div class="text-xs font-bold px-2 py-1 bg-green-100 text-green-800 rounded-full uppercase">
                                            Status: Paid
                                        </div>
                                    </div>
                                    <h4 class="text-2xl font-bold text-gray-900 mb-2">{{ $item->ticket->event->title }}</h4>
                                    
                                    <p class="text-gray-600 text-sm mb-4">
                                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                        {{ $item->ticket->event->location_name }}
                                    </p>
                                </div>
                                
                                <div class="mt-4 pt-4 border-t border-gray-100 flex items-center justify-between">
                                    <div class="text-sm">
                                        <span class="text-gray-500 block">Quantity</span>
                                        <span class="font-bold text-gray-900">{{ $item->quantity }} x Tickets</span>
                                    </div>
                                    <a href="#" class="px-5 py-2.5 bg-indigo-50 text-indigo-700 font-bold rounded-lg hover:bg-indigo-100 transition hidden md:block">
                                        View E-Ticket PDF
                                    </a>
                                </div>
                            </div>

                            <!-- Tear-off stub effect -->
                            <div class="hidden md:flex flex-col items-center justify-center w-32 border-l-2 border-dashed border-gray-300 bg-gray-50 relative">
                                <div class="absolute -top-3 -left-3 w-6 h-6 bg-gray-100 rounded-full"></div>
                                <div class="absolute -bottom-3 -left-3 w-6 h-6 bg-gray-100 rounded-full"></div>
                                <div class="p-4 text-center">
                                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data={{ $order->order_number }}" class="mx-auto mb-2 opacity-80 w-16 h-16" alt="QR">
                                    <span class="text-xs text-gray-400 font-mono">{{ substr($order->order_number, 0, 8) }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @empty
                    <div class="bg-white p-12 text-center rounded-2xl shadow-sm">
                        <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path></svg>
                        </div>
                        <h4 class="text-xl font-bold text-gray-800 mb-2">No tickets yet</h4>
                        <p class="text-gray-500 mb-6">You haven't purchased any tickets. Discover amazing events now!</p>
                        <a href="/" class="px-6 py-3 bg-indigo-600 text-white font-bold rounded-lg shadow-md hover:bg-indigo-700 transition inline-block">
                            Browse Events
                        </a>
                    </div>
                @endforelse
            </div>

        </div>
    </div>
</x-app-layout>
