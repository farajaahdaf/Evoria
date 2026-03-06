<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $event->title }} - Evoria</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="antialiased bg-gray-50 text-gray-900 font-sans" x-data="{ bookingModal: false, selectedTicket: null }">
    
    <!-- Navbar (Simplified) -->
    <nav class="bg-white border-b border-gray-200 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <a href="/" class="text-2xl font-bold tracking-tight text-blue-600">Evo<span class="text-yellow-400">ria</span></a>
                <div class="flex items-center space-x-4">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="font-bold text-gray-600 hover:text-blue-600 transition">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="font-bold text-gray-600 hover:text-blue-600 transition">Log in</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Event Hero -->
    <div class="relative bg-gray-900 h-96">
        @if($event->banner_path)
            <img src="{{ asset('storage/' . $event->banner_path) }}" class="w-full h-full object-cover opacity-60">
        @else
            <img src="https://images.unsplash.com/photo-1540575467063-178a50c2df87?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80" class="w-full h-full object-cover opacity-60">
        @endif
        <div class="absolute inset-0 bg-gradient-to-t from-gray-900 via-transparent to-transparent"></div>
        <div class="absolute bottom-0 w-full">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-10">
                <div class="inline-block px-3 py-1 bg-yellow-400 text-blue-900 text-sm font-bold rounded-md mb-4">{{ $event->category->name ?? 'Event' }}</div>
                <h1 class="text-4xl md:text-5xl font-extrabold text-white tracking-tight mb-2 shadow-sm">{{ $event->title }}</h1>
                <p class="text-xl text-gray-300">{{ $event->organizer->profile->company_name ?? $event->organizer->name }}</p>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
            
            <!-- Details Column -->
            <div class="lg:col-span-2 space-y-10">
                
                @if(session('success'))
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-sm">
                        <p>{{ session('success') }}</p>
                    </div>
                @endif
                @if(session('error'))
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-sm">
                        <p>{{ session('error') }}</p>
                    </div>
                @endif

                <section>
                    <h2 class="text-2xl font-bold text-gray-900 mb-4 border-b pb-2">Description</h2>
                    <div class="prose max-w-none text-gray-600 leading-relaxed whitespace-pre-line">
                        {{ $event->description }}
                    </div>
                </section>

                <section>
                    <h2 class="text-2xl font-bold text-gray-900 mb-4 border-b pb-2">Time & Location</h2>
                    <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm flex flex-col sm:flex-row sm:space-x-8 space-y-4 sm:space-y-0">
                        <div class="flex items-start space-x-4">
                            <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center shrink-0">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900">Date & Time</h4>
                                <p class="text-gray-600 mt-1">{{ $event->start_time->format('l, d F Y') }}<br>{{ $event->start_time->format('H:i') }} - {{ $event->end_time->format('H:i') }}</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-4">
                            <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center shrink-0">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900">Location</h4>
                                <p class="text-gray-600 mt-1 font-medium">{{ $event->location_name }}</p>
                                <p class="text-gray-500 text-sm mt-0.5">{{ $event->address }}</p>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            <!-- Tickets Sticky Sidebar -->
            <div class="lg:col-span-1">
                <div class="sticky top-24 bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                    <div class="bg-blue-600 p-6 text-white text-center">
                        <h3 class="text-xl font-bold">Select Tickets</h3>
                        <p class="text-blue-100 mt-1 opacity-80 text-sm">Secure your spot before it sells out.</p>
                    </div>
                    <div class="p-6 space-y-4">
                        @foreach($event->tickets as $ticket)
                            <div class="border rounded-xl p-4 {{ $ticket->available_qty > 0 ? 'border-gray-200 hover:border-blue-500 transition cursor-pointer' : 'border-gray-200 bg-gray-50 opacity-60' }}"
                                 @if($ticket->available_qty > 0) @click="selectedTicket = {id: {{ $ticket->id }}, name: '{{ $ticket->name }}', price: {{ $ticket->price }}}; bookingModal = true;" @endif>
                                <div class="flex justify-between items-center mb-2">
                                    <h4 class="font-bold text-lg text-gray-900">{{ $ticket->name }}</h4>
                                    <span class="font-bold text-blue-600">
                                        {{ $ticket->price > 0 ? 'Rp ' . number_format($ticket->price, 0, ',', '.') : 'FREE' }}
                                    </span>
                                </div>
                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-gray-500">Available: {{ $ticket->available_qty }} / {{ $ticket->quota }}</span>
                                    @if($ticket->available_qty == 0)
                                        <span class="text-red-500 font-bold">SOLD OUT</span>
                                    @else
                                        <span class="text-green-500 font-bold">Available</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Booking Modal (AlpineJS) -->
    <div x-cloak x-show="bookingModal" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-50 backdrop-blur-sm"
        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full mx-4 overflow-hidden" @click.away="bookingModal = false" x-show="bookingModal"
            x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
            
            <div class="bg-gray-50 p-6 border-b flex justify-between items-center">
                <h3 class="text-xl font-bold text-gray-900">Checkout Simulation</h3>
                <button @click="bookingModal = false" class="text-gray-400 hover:text-gray-600">&times;</button>
            </div>
            
            <form action="{{ route('attendee.book', $event->id) }}" method="POST" class="p-6 space-y-6">
                @csrf
                <input type="hidden" name="ticket_id" x-model="selectedTicket.id">
                
                <div class="flex justify-between items-center bg-blue-50 p-4 rounded-lg">
                    <div>
                        <p class="text-sm text-blue-800 opacity-80">Selected Ticket</p>
                        <p class="font-bold text-blue-900 text-lg" x-text="selectedTicket?.name"></p>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Quantity</label>
                    <select name="quantity" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                        <option value="1">1 Ticket</option>
                        <option value="2">2 Tickets</option>
                        <option value="3">3 Tickets</option>
                        <option value="4">4 Tickets</option>
                        <option value="5">5 Tickets</option>
                    </select>
                </div>

                <div class="pt-4 border-t border-gray-100 flex justify-between items-center">
                    <span class="text-gray-500 text-sm italic">Simulated payment flow</span>
                    <button type="submit" class="bg-blue-600 text-white font-bold py-3 px-6 rounded-lg hover:bg-blue-700 transition shadow">
                        Book Now
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
