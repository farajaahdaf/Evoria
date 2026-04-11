<!DOCTYPE html>
<html class="light" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Tiket Saya - {{ config('app.name', 'Evoria') }}</title>

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-[#ebebeb] min-h-screen text-slate-900">
    <x-attendee-main-header />

    <main class="max-w-[1200px] mx-auto px-6 py-10 space-y-8">
        @php
            $totalOrders = $orders->count();
            $paidOrders = $orders->where('status', 'paid')->count();
            $upcomingEvents = 0;
            foreach($orders->where('status', 'paid') as $order) {
                foreach($order->orderItems as $item) {
                    if ($item->ticket && $item->ticket->event && \Carbon\Carbon::parse($item->ticket->event->start_time)->isFuture()) {
                        $upcomingEvents++;
                    }
                }
            }
        @endphp

        <!-- 1. Hero Section -->
        <section class="bg-gradient-to-r from-violet-600 to-blue-600 rounded-2xl p-8 md:p-10 shadow-lg text-white">
            <h1 class="text-3xl md:text-4xl font-black mb-2">Hi, {{ auth()->user()->name }}! 👋</h1>
            <p class="text-blue-100 text-lg">Manage your tickets and upcoming events</p>
        </section>

        <!-- 2. Summary Cards -->
        <section class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center text-slate-600">
                    <span class="material-symbols-outlined">receipt_long</span>
                </div>
                <div>
                    <p class="text-sm text-slate-500 font-medium">Total Orders</p>
                    <p class="text-2xl font-black text-slate-900">{{ $totalOrders }}</p>
                </div>
            </div>
            <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-green-50 flex items-center justify-center text-green-600">
                    <span class="material-symbols-outlined">check_circle</span>
                </div>
                <div>
                    <p class="text-sm text-slate-500 font-medium">Paid Orders</p>
                    <p class="text-2xl font-black text-slate-900">{{ $paidOrders }}</p>
                </div>
            </div>
            <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-blue-50 flex items-center justify-center text-blue-600">
                    <span class="material-symbols-outlined">event</span>
                </div>
                <div>
                    <p class="text-sm text-slate-500 font-medium">Upcoming Events</p>
                    <p class="text-2xl font-black text-slate-900">{{ $upcomingEvents }}</p>
                </div>
            </div>
        </section>

        @if(session('success'))
            <div class="bg-green-100 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm font-semibold">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm font-semibold">
                {{ session('error') }}
            </div>
        @endif

        <!-- 3. Tampilan Daftar Order -->
        <section class="space-y-6">
            <h2 class="text-2xl font-bold text-slate-900">Your Tickets</h2>
            
            <div class="space-y-5">
            @forelse($orders as $order)
                @foreach($order->orderItems as $item)
                    @php
                        $event = $item->ticket->event;
                        $fallbackBanner = 'https://images.unsplash.com/photo-1540575467063-178a50c2df87?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80';
                        $bannerUrl = $fallbackBanner;

                        if ($event && filled($event->banner_path)) {
                            if (\Illuminate\Support\Str::startsWith($event->banner_path, ['http://', 'https://'])) {
                                $bannerUrl = $event->banner_path;
                            } else {
                                $normalizedPath = ltrim(preg_replace('#^/?storage/#', '', $event->banner_path), '/');
                                $bannerUrl = \Illuminate\Support\Facades\Storage::url($normalizedPath);
                            }
                        }

                         $borderColor = match($order->status) {
                            'paid' => 'border-l-green-500',
                            'pending' => 'border-l-amber-500',
                            default => 'border-l-red-500',
                        };
                    @endphp
                    <article class="bg-white rounded-2xl border border-slate-100 border-l-4 {{ $borderColor }} overflow-hidden shadow-sm hover:shadow-lg transition-shadow duration-300">
                        <div class="flex flex-col lg:flex-row">
                            <div class="w-full lg:w-[280px] h-48 lg:h-auto bg-slate-200 relative">
                                <img src="{{ $bannerUrl }}" alt="{{ $event->title ?? 'Banner Event' }}" class="w-full h-full object-cover">
                            </div>

                            <div class="flex-1 p-6 flex flex-col justify-between">
                                <div>
                                    <div class="flex flex-wrap items-start justify-between gap-3 mb-2">
                                        <h3 class="text-xl font-bold text-slate-900 leading-tight">
                                            {{ $event->title ?? 'Event Tidak Tersedia' }}
                                        </h3>
                                        <span @class([
                                            'px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider',
                                            'bg-green-100 text-green-700' => $order->status === 'paid',
                                            'bg-amber-100 text-amber-700' => $order->status === 'pending',
                                            'bg-red-100 text-red-700' => in_array($order->status, ['failed', 'cancelled', 'refunded'], true),
                                        ])>
                                            {{ $order->status }}
                                        </span>
                                    </div>
                                    
                                    <div class="flex items-center gap-2 text-sm text-slate-500 mb-4">
                                        <span class="material-symbols-outlined text-[16px]">calendar_month</span>
                                        <span>{{ $event ? \Carbon\Carbon::parse($event->start_time)->translatedFormat('d M Y, H:i') : '-' }}</span>
                                    </div>

                                    <div class="bg-slate-50 rounded-xl p-4 grid grid-cols-2 md:grid-cols-4 gap-4">
                                        <div>
                                            <p class="text-xs text-slate-500 mb-1">Ticket Type</p>
                                            <p class="font-bold text-slate-900">{{ $item->ticket->name ?? 'Deleted' }}</p>
                                        </div>
                                        <div>
                                            <p class="text-xs text-slate-500 mb-1">Quantity</p>
                                            <p class="font-bold text-slate-900">{{ $item->quantity }} pcs</p>
                                        </div>
                                        <div>
                                            <p class="text-xs text-slate-500 mb-1">Total Price</p>
                                            <p class="font-bold text-slate-900">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</p>
                                        </div>
                                        <div>
                                            <p class="text-xs text-slate-500 mb-1">Order No.</p>
                                            <p class="font-bold text-slate-900">{{ $order->order_number }}</p>
                                        </div>
                                    </div>
                                    
                                    @if($order->status === 'paid' && $item->eTickets && $item->eTickets->count() > 0)
                                        <div class="mt-4 pt-4 border-t border-slate-100">
                                            <p class="text-xs text-slate-500 mb-2">E-Ticket Codes</p>
                                            <div class="flex flex-wrap gap-2">
                                                @foreach($item->eTickets as $et)
                                                    <span class="px-3 py-1.5 bg-slate-100 border border-slate-200 rounded-lg text-sm font-mono text-slate-700 font-bold">
                                                        {{ $et->ticket_code }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                @if($order->status === 'pending' && $order->snap_token && $midtransEnabled)
                                    <div class="mt-5 pt-4 border-t border-slate-100 flex flex-wrap items-center justify-between gap-3">
                                        <p class="text-sm text-amber-600 font-medium">Awaiting payment completion...</p>
                                        <button
                                            type="button"
                                            class="inline-flex items-center rounded-lg bg-primary px-6 py-2.5 text-sm font-bold text-white hover:bg-primary/90 transition-colors shadow-md hover:shadow-lg"
                                            onclick="payPendingOrder('{{ $order->snap_token }}', {{ $order->id }})"
                                        >
                                            Pay Now
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </article>
                @endforeach
            @empty
                <!-- 4. Empty State -->
                <div class="bg-white rounded-2xl border border-slate-100 p-12 text-center shadow-sm">
                    <svg class="w-32 h-32 mx-auto text-slate-200 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
                    </svg>
                    <h3 class="text-2xl font-black text-slate-900 mb-2">Find your first event!</h3>
                    <p class="text-slate-500 mb-8 max-w-md mx-auto">Discover amazing local and global events happening right now and get your tickets easily.</p>
                    <a href="{{ route('home') }}" class="inline-flex items-center justify-center px-8 py-3 rounded-xl bg-gradient-to-r from-violet-600 to-blue-600 text-white font-bold hover:brightness-110 transition-all shadow-md hover:shadow-lg">
                        Explore Events
                    </a>
                </div>
            @endforelse
            </div>
        </section>
    </main>

    @if($midtransEnabled)
        <script
            type="text/javascript"
            src="{{ $midtransSnapJsUrl }}"
            data-client-key="{{ $midtransClientKey }}"
        ></script>
        <script>
            async function syncOrderStatus(orderId) {
                if (!orderId) return;

                try {
                    await fetch(`/attendee/orders/${orderId}/refresh-status`, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                    });
                } catch (error) {
                    console.warn('Gagal sinkronisasi status order:', error);
                }
            }

            function payPendingOrder(snapToken, orderId) {
                if (!window.snap) {
                    alert('Snap.js Midtrans belum termuat.');
                    return;
                }

                window.snap.pay(snapToken, {
                    onSuccess: async function () {
                        await syncOrderStatus(orderId);
                        window.location.reload();
                    },
                    onPending: async function () {
                        await syncOrderStatus(orderId);
                        window.location.reload();
                    },
                    onError: function () {
                        alert('Pembayaran gagal diproses. Silakan coba lagi.');
                    },
                    onClose: async function () {
                        await syncOrderStatus(orderId);
                        window.location.reload();
                    }
                });
            }
        </script>
    @endif
</body>
</html>
