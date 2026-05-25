<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $event->title }} - Evoria</title>
    <link rel="icon" type="image/png" href="{{ asset('images/favicon-icon.png') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#2563EB",
                        "hero-bg": "#F8F9FA",
                        "dark-footer": "#0F172A",
                    },
                    fontFamily: {
                        "sans": ["Plus Jakarta Sans", "sans-serif"],
                    },
                },
            },
        }
    </script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="antialiased bg-[#ebebeb] text-gray-900 font-sans" x-data="bookingFlow()">
    @php
        $bannerUrl = 'https://images.unsplash.com/photo-1540575467063-178a50c2df87?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80';

        if (filled($event->banner_path)) {
            if (\Illuminate\Support\Str::startsWith($event->banner_path, ['http://', 'https://'])) {
                $bannerUrl = $event->banner_path;
            } else {
                $normalizedPath = ltrim(preg_replace('#^/?storage/#', '', $event->banner_path), '/');
                $bannerUrl = asset('storage/' . $normalizedPath);
            }
        }
    @endphp
    
    @auth
        @if(auth()->user()->role === 'attendee')
            <x-attendee-main-header />
        @else
            <header class="bg-white border-b border-gray-200 sticky top-0 z-40">
                <div class="max-w-[1400px] mx-auto px-6 h-[80px] flex items-center justify-between gap-6">
                    <div class="flex items-center gap-8">
                        <a href="{{ route('home') }}" class="flex items-center gap-1 shrink-0">
                            <x-application-logo class="h-10 w-auto" />
                        </a>
                    </div>
                    <div class="flex items-center gap-4">
                        <a href="{{ url('/dashboard') }}" class="px-6 py-[10px] text-[14px] font-bold border border-primary text-primary rounded-lg hover:bg-primary/5 transition-colors">Dashboard</a>
                    </div>
                </div>
            </header>
        @endif
    @else
        <header class="bg-white border-b border-gray-200 sticky top-0 z-40">
            <div class="max-w-[1400px] mx-auto px-6 h-[80px] flex items-center justify-between gap-6">
                <div class="flex items-center gap-8">
                    <a href="{{ route('home') }}" class="flex items-center gap-1 shrink-0">
                        <x-application-logo class="h-10 w-auto" />
                    </a>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('register') }}" class="px-6 py-[10px] text-[14px] font-bold border border-primary text-primary rounded-lg hover:bg-primary/5 transition-colors">Daftar</a>
                    <a href="{{ route('login') }}" class="px-6 py-[10px] bg-primary text-white text-[14px] font-bold rounded-lg hover:bg-primary/90 transition-all shadow-sm">Masuk</a>
                </div>
            </div>
        </header>
    @endauth

    <!-- Event Hero -->
    <div class="relative bg-gray-900 h-96">
        <img src="{{ $bannerUrl }}" alt="{{ $event->title }}" class="w-full h-full object-cover opacity-60">
        <div class="absolute inset-0 bg-gradient-to-t from-gray-900 via-transparent to-transparent"></div>
        <div class="absolute bottom-0 w-full">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-10">
                <div class="inline-block px-3 py-1 bg-yellow-400 text-blue-900 text-sm font-bold rounded-md mb-4">{{ $event->category->name ?? 'Event' }}</div>
                <h1 class="text-4xl md:text-5xl font-extrabold text-white tracking-tight mb-2 shadow-sm">{{ $event->title }}</h1>
                <p class="text-xl text-gray-300">{{ $event->organizer->organizerProfile->company_name ?? $event->organizer->name }}</p>
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

                <section class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                    <h2 class="text-2xl font-bold text-gray-900 border-b border-gray-200 pb-3 mb-4">Description</h2>
                    <div class="prose max-w-none text-gray-700 leading-relaxed whitespace-pre-line">
                        {{ $event->description }}
                    </div>
                </section>

                <section class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                    <h2 class="text-2xl font-bold text-gray-900 border-b border-gray-200 pb-3 mb-4">Time & Location</h2>
                    <div class="flex flex-col sm:flex-row sm:space-x-8 space-y-4 sm:space-y-0">
                        <div class="flex items-start space-x-4">
                            <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center shrink-0">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900">Date & Time</h4>
                                <p class="text-gray-700 leading-relaxed mt-1">{{ $event->start_time->format('l, d F Y') }}<br>{{ $event->start_time->format('H:i') }} - {{ $event->end_time->format('H:i') }}</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-4">
                            <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center shrink-0">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900">Location</h4>
                                <p class="text-gray-700 leading-relaxed mt-1 font-medium">{{ $event->location_name }}</p>
                                <p class="text-gray-500 text-sm mt-0.5 leading-relaxed">{{ $event->address }}</p>
                            </div>
                        </div>
                    </div>
                </section>

                @if($event->latitude && $event->longitude)
                    <section class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                        <div class="flex items-center justify-between gap-4 border-b border-gray-200 pb-3 mb-4">
                            <h2 class="text-2xl font-bold text-gray-900">Event Map</h2>
                            <a
                                href="https://www.google.com/maps/search/?api=1&query={{ $event->latitude }},{{ $event->longitude }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="inline-flex items-center rounded-full border border-blue-200 px-4 py-2 text-sm font-bold text-blue-600 transition hover:bg-blue-50"
                            >
                                Buka di Google Maps
                            </a>
                        </div>
                        <div class="overflow-hidden rounded-xl border border-gray-100 bg-gray-50">
                            <div id="event-map" class="h-[360px] w-full"></div>
                        </div>
                    </section>
                @endif
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
                                 @if($ticket->available_qty > 0) @click="selectTicket({id: {{ $ticket->id }}, name: @js($ticket->name), price: {{ $ticket->price }}})" @endif>
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

    <!-- Virtual Waiting Room Overlay -->
    <div x-cloak x-show="wrOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/70 backdrop-blur-sm"
        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">

        <div class="bg-white rounded-3xl shadow-2xl max-w-md w-full mx-4 overflow-hidden text-center"
            x-show="wrOpen"
            x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">

            <div class="bg-gradient-to-br from-blue-600 to-indigo-700 px-8 py-7 text-white">
                <div class="mx-auto mb-3 h-12 w-12 rounded-full bg-white/15 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[28px]">groups</span>
                </div>
                <h3 class="text-xl font-extrabold">Ruang Tunggu Tiket</h3>
                <p class="text-blue-100 text-sm mt-1" x-text="selectedTicket?.name"></p>
            </div>

            <div class="p-8 space-y-5">
                <!-- Joining -->
                <template x-if="wrStatus === 'joining'">
                    <div class="space-y-3">
                        <div class="mx-auto h-10 w-10 border-4 border-blue-100 border-t-blue-600 rounded-full animate-spin"></div>
                        <p class="text-slate-600 font-semibold">Menghubungkan ke antrian...</p>
                    </div>
                </template>

                <!-- Waiting -->
                <template x-if="wrStatus === 'waiting'">
                    <div class="space-y-4">
                        <div>
                            <p class="text-sm text-slate-500">Posisi antrian Anda</p>
                            <p class="text-5xl font-black text-blue-600 leading-tight" x-text="'#' + wrPosition"></p>
                            <p class="text-sm text-slate-500 mt-1">
                                dari <span class="font-bold text-slate-700" x-text="wrTotal"></span> orang menunggu
                            </p>
                        </div>
                        <div class="bg-blue-50 rounded-xl py-3 px-4">
                            <p class="text-sm text-blue-800">Estimasi giliran: <span class="font-bold" x-text="estimateText()"></span></p>
                        </div>
                        <div class="h-2 w-full bg-slate-100 rounded-full overflow-hidden">
                            <div class="h-full bg-blue-600 rounded-full animate-pulse" style="width:100%"></div>
                        </div>
                        <p class="text-xs text-slate-400">Jangan tutup halaman ini. Anda akan otomatis masuk saat giliran tiba.</p>
                        <button @click="leaveWaitingRoom()" class="text-sm font-semibold text-slate-500 hover:text-red-500 transition">
                            Keluar dari antrian
                        </button>
                    </div>
                </template>

                <!-- Expired / dropped -->
                <template x-if="wrStatus === 'expired'">
                    <div class="space-y-4">
                        <div class="mx-auto h-12 w-12 rounded-full bg-amber-100 flex items-center justify-center">
                            <span class="material-symbols-outlined text-amber-600 text-[28px]">hourglass_disabled</span>
                        </div>
                        <p class="text-slate-700 font-semibold">Sesi antrian berakhir</p>
                        <p class="text-sm text-slate-500">Waktu Anda habis atau sesi terputus. Silakan masuk antrian lagi.</p>
                        <div class="flex gap-3">
                            <button @click="leaveWaitingRoom()" class="flex-1 py-2.5 rounded-lg border border-slate-200 font-semibold text-slate-600 hover:bg-slate-50">Tutup</button>
                            <button @click="enterWaitingRoom()" class="flex-1 py-2.5 rounded-lg bg-blue-600 text-white font-bold hover:bg-blue-700">Antri Lagi</button>
                        </div>
                    </div>
                </template>

                <!-- Error -->
                <template x-if="wrStatus === 'error'">
                    <div class="space-y-4">
                        <div class="mx-auto h-12 w-12 rounded-full bg-red-100 flex items-center justify-center">
                            <span class="material-symbols-outlined text-red-600 text-[28px]">error</span>
                        </div>
                        <p class="text-slate-700 font-semibold" x-text="wrError || 'Terjadi kesalahan.'"></p>
                        <div class="flex gap-3">
                            <button @click="leaveWaitingRoom()" class="flex-1 py-2.5 rounded-lg border border-slate-200 font-semibold text-slate-600 hover:bg-slate-50">Tutup</button>
                            <button @click="enterWaitingRoom()" class="flex-1 py-2.5 rounded-lg bg-blue-600 text-white font-bold hover:bg-blue-700">Coba Lagi</button>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <!-- Booking Modal (AlpineJS) -->
    <div x-cloak x-show="bookingModal" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-50 backdrop-blur-sm"
        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full mx-4 overflow-hidden" @click.away="closeBooking()" x-show="bookingModal"
            x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
            
            <div class="bg-gray-50 p-6 border-b flex justify-between items-center">
                <h3 class="text-xl font-bold text-gray-900">Checkout Simulation</h3>
                <button @click="closeBooking()" class="text-gray-400 hover:text-gray-600">&times;</button>
            </div>

            <form
                action="{{ route('attendee.book', $event->id) }}"
                method="POST"
                class="p-6 space-y-6"
                onsubmit="return handleMidtransCheckout(event, this)"
            >
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
                    <span class="text-gray-500 text-sm italic">
                        {{ $midtransEnabled ? 'Pembayaran aman via Midtrans Snap' : 'Midtrans belum dikonfigurasi' }}
                    </span>
                    <button
                        type="submit"
                        class="bg-[#10367d] text-white font-bold py-3 px-6 rounded-lg hover:bg-[#0c2a61] transition shadow disabled:opacity-60 disabled:cursor-not-allowed"
                        :disabled="!selectedTicket"
                    >
                        Bayar Sekarang
                    </button>
                </div>
            </form>
        </div>
    </div>

    @if($midtransEnabled)
        <script
            type="text/javascript"
            src="{{ $midtransSnapJsUrl }}"
            data-client-key="{{ $midtransClientKey }}"
        ></script>
    @endif
    @if($googleMapsWebApiKey && $event->latitude && $event->longitude)
        <script
            async
            defer
            src="https://maps.googleapis.com/maps/api/js?key={{ $googleMapsWebApiKey }}"
        ></script>
    @endif
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('bookingFlow', () => ({
                bookingModal: false,
                selectedTicket: null,

                wrOpen: false,
                wrStatus: 'idle',
                wrPosition: null,
                wrTotal: null,
                wrEstimate: null,
                wrError: '',
                wrPoll: null,

                eventId: {{ $event->id }},
                isAttendee: {{ (auth()->check() && auth()->user()->role === 'attendee') ? 'true' : 'false' }},
                loginUrl: @js(route('login')),

                selectTicket(ticket) {
                    this.selectedTicket = ticket;
                    if (!this.isAttendee) {
                        window.location.href = this.loginUrl;
                        return;
                    }
                    this.enterWaitingRoom();
                },

                async enterWaitingRoom() {
                    this.wrOpen = true;
                    this.wrStatus = 'joining';
                    this.wrError = '';
                    try {
                        const data = await this.postJson(`/attendee/queue/${this.eventId}/join`);
                        this.applyStatus(data);
                        if (this.wrStatus === 'waiting') this.startPolling();
                    } catch (e) {
                        this.wrStatus = 'error';
                        this.wrError = e.message || 'Gagal masuk antrian.';
                    }
                },

                startPolling() {
                    this.stopPolling();
                    this.wrPoll = setInterval(() => this.pollStatus(), 3000);
                },
                stopPolling() {
                    if (this.wrPoll) { clearInterval(this.wrPoll); this.wrPoll = null; }
                },

                async pollStatus() {
                    try {
                        const data = await this.getJson(`/attendee/queue/${this.eventId}/status`);
                        this.applyStatus(data);
                    } catch (e) { /* keep polling */ }
                },

                applyStatus(data) {
                    this.wrStatus = data.status;
                    if (data.status === 'waiting') {
                        this.wrPosition = data.position;
                        this.wrTotal = data.total_waiting;
                        this.wrEstimate = data.estimate_seconds;
                    } else if (data.status === 'admitted') {
                        this.stopPolling();
                        this.wrOpen = false;
                        this.bookingModal = true;
                    } else if (data.status === 'expired') {
                        this.stopPolling();
                    }
                },

                async leaveWaitingRoom() {
                    this.stopPolling();
                    this.wrOpen = false;
                    this.wrStatus = 'idle';
                    try { await this.postJson(`/attendee/queue/${this.eventId}/leave`); } catch (e) {}
                },

                closeBooking() {
                    this.bookingModal = false;
                    this.leaveWaitingRoom();
                },

                estimateText() {
                    const s = this.wrEstimate || 0;
                    if (s < 60) return `± ${s} detik`;
                    return `± ${Math.ceil(s / 60)} menit`;
                },

                csrf() {
                    return document.querySelector('meta[name="csrf-token"]')?.content || '';
                },
                async postJson(url) {
                    const r = await fetch(url, { method: 'POST', headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': this.csrf() } });
                    const d = await r.json().catch(() => ({}));
                    if (!r.ok) throw new Error(d.message || 'Terjadi kesalahan.');
                    return d;
                },
                async getJson(url) {
                    const r = await fetch(url, { headers: { 'Accept': 'application/json' } });
                    const d = await r.json().catch(() => ({}));
                    if (!r.ok) throw new Error(d.message || 'Terjadi kesalahan.');
                    return d;
                },
            }));
        });

        @if($googleMapsWebApiKey && $event->latitude && $event->longitude)
        document.addEventListener('DOMContentLoaded', function () {
            const initMap = () => {
                if (!window.google || !window.google.maps) {
                    return;
                }

                const location = {
                    lat: {{ (float) $event->latitude }},
                    lng: {{ (float) $event->longitude }},
                };

                const map = new google.maps.Map(document.getElementById('event-map'), {
                    center: location,
                    zoom: 15,
                    mapTypeControl: false,
                    streetViewControl: false,
                    fullscreenControl: false,
                });

                new google.maps.Marker({
                    position: location,
                    map,
                    title: @json($event->title),
                });
            };

            const waitForGoogleMaps = () => {
                if (window.google && window.google.maps) {
                    initMap();
                    return;
                }

                window.setTimeout(waitForGoogleMaps, 150);
            };

            waitForGoogleMaps();
        });
        @endif

        async function handleMidtransCheckout(event, form) {
            event.preventDefault();
            const submitButton = form.querySelector('button[type="submit"]');
            const originalLabel = submitButton ? submitButton.textContent : '';

            if (!window.snap && {{ $midtransEnabled ? 'true' : 'false' }}) {
                alert('Snap.js Midtrans belum termuat.');
                return false;
            }

            if (submitButton) {
                submitButton.disabled = true;
                submitButton.textContent = 'Memproses...';
            }

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value,
                    },
                    body: new FormData(form),
                });

                const payload = await response.json();

                if (!response.ok) {
                    throw new Error(payload.message || 'Gagal membuat transaksi.');
                }

                if (!payload.snap_token) {
                    window.location.href = payload.redirect_url || "{{ route('attendee.dashboard') }}";
                    return false;
                }

                window.snap.pay(payload.snap_token, {
                    onSuccess: async function () {
                        await syncOrderStatus(payload.order_id);
                        window.location.href = "{{ route('attendee.dashboard') }}";
                    },
                    onPending: async function () {
                        await syncOrderStatus(payload.order_id);
                        window.location.href = "{{ route('attendee.dashboard') }}";
                    },
                    onError: function () {
                        alert('Pembayaran gagal diproses. Silakan coba lagi.');
                    },
                    onClose: async function () {
                        await syncOrderStatus(payload.order_id);
                        window.location.href = "{{ route('attendee.dashboard') }}";
                    }
                });
            } catch (error) {
                alert(error.message || 'Terjadi kesalahan saat memulai pembayaran.');
            } finally {
                if (submitButton) {
                    submitButton.disabled = false;
                    submitButton.textContent = originalLabel;
                }
            }

            return false;
        }

        async function syncOrderStatus(orderId) {
            if (!orderId) return;

            try {
                await fetch(`/attendee/orders/${orderId}/refresh-status`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                            || document.querySelector('input[name="_token"]')?.value,
                    },
                });
            } catch (error) {
                console.warn('Gagal sinkronisasi status order:', error);
            }
        }
    </script>
</body>
</html>
