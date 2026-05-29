<!DOCTYPE html>
<html class="light" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Tiket Saya - {{ config('app.name', 'Evoria') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/favicon-icon.png') }}">

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
        <section class="relative bg-gray-900 rounded-3xl overflow-hidden shadow-2xl">
            <div class="absolute inset-0 bg-gradient-to-r from-blue-900/60 to-transparent"></div>
            <div class="relative p-10 flex items-center justify-between">
                <div class="max-w-lg space-y-4">
                    <p class="text-xs font-black text-blue-200 uppercase tracking-[0.25em]">My Tickets</p>
                    <h3 class="text-4xl font-extrabold text-white tracking-tight">Hi, {{ auth()->user()->name }}!</h3>
                    <p class="text-gray-300 text-lg leading-relaxed">
                        Anda memiliki {{ $paidOrders }} pesanan berbayar dan {{ $upcomingEvents }} event yang akan datang.
                    </p>
                    <div class="pt-2">
                        <a href="{{ route('home') }}" class="inline-flex items-center px-6 py-3 bg-white text-gray-900 font-bold rounded-xl hover:bg-gray-100 transition-all shadow-lg text-sm">
                            Cari Event Baru
                        </a>
                    </div>
                </div>
                <div class="hidden md:flex pr-6 opacity-80">
                    <span class="material-symbols-outlined text-white" style="font-size:120px;">confirmation_number</span>
                </div>
            </div>
        </section>

        <!-- 2. Summary Cards -->
        <section class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="block bg-white rounded-3xl p-6 shadow-sm border border-gray-100 relative overflow-hidden group transition hover:-translate-y-1 hover:shadow-md">
                <div class="flex justify-between items-start mb-4">
                    <div class="p-3 bg-gray-50 rounded-2xl text-gray-900 group-hover:bg-blue-600 group-hover:text-white transition-all duration-300">
                        <span class="material-symbols-outlined" style="font-size:24px;">receipt_long</span>
                    </div>
                </div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Total Orders</p>
                <h4 class="text-3xl font-black text-gray-900">{{ $totalOrders }}</h4>
            </div>
            <div class="block bg-white rounded-3xl p-6 shadow-sm border border-gray-100 relative overflow-hidden group transition hover:-translate-y-1 hover:shadow-md">
                <div class="flex justify-between items-start mb-4">
                    <div class="p-3 bg-green-50 rounded-2xl text-green-700 group-hover:bg-green-600 group-hover:text-white transition-all duration-300">
                        <span class="material-symbols-outlined" style="font-size:24px;">check_circle</span>
                    </div>
                </div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Paid Orders</p>
                <h4 class="text-3xl font-black text-gray-900">{{ $paidOrders }}</h4>
            </div>
            <div class="block bg-white rounded-3xl p-6 shadow-sm border border-gray-100 relative overflow-hidden group transition hover:-translate-y-1 hover:shadow-md">
                <div class="flex justify-between items-start mb-4">
                    <div class="p-3 bg-blue-50 rounded-2xl text-blue-700 group-hover:bg-blue-600 group-hover:text-white transition-all duration-300">
                        <span class="material-symbols-outlined" style="font-size:24px;">event</span>
                    </div>
                </div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Upcoming Events</p>
                <h4 class="text-3xl font-black text-gray-900">{{ $upcomingEvents }}</h4>
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
                    <article id="order-{{ $order->id }}" data-order-id="{{ $order->id }}"
                             class="order-card bg-white rounded-2xl border border-slate-100 border-l-4 {{ $borderColor }} overflow-hidden shadow-sm hover:shadow-lg transition-shadow duration-300">
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
                                            <p class="text-xs text-slate-500 mb-3 font-semibold">Your E-Tickets</p>
                                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                                                @foreach($item->eTickets as $et)
                                                    <div class="bg-white border text-center rounded-xl p-4 flex flex-col items-center shadow-sm relative overflow-hidden">
                                                        <button type="button"
                                                                onclick="downloadQR(this, '{{ $et->ticket_code }}', {{ json_encode(($event->title ?? 'event') . ' - ' . ($item->ticket->name ?? 'ticket')) }})"
                                                                class="qr-wrap group relative w-[150px] h-[150px] flex items-center justify-center bg-white p-2 rounded-lg border border-slate-100 mb-3
                                                                       hover:border-primary hover:shadow-md transition cursor-pointer">
                                                            <div class="w-full h-full [&>svg]:w-full [&>svg]:h-full">
                                                                {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(150)->margin(0)->color(16, 54, 125)->generate($et->ticket_code) !!}
                                                            </div>
                                                            @if(isset($et->status) && $et->status === 'used')
                                                                <div class="absolute inset-0 bg-white/80 flex items-center justify-center z-10 rounded-lg">
                                                                    <span class="bg-slate-800 text-white text-[10px] font-bold px-2 py-1 rounded rotate-[-15deg] whitespace-nowrap shadow-md">SUDAH DIGUNAKAN</span>
                                                                </div>
                                                            @endif
                                                            {{-- Download overlay (hover) --}}
                                                            <div class="absolute inset-0 bg-slate-900/60 opacity-0 group-hover:opacity-100 transition-opacity
                                                                        flex flex-col items-center justify-center gap-1 rounded-lg z-20">
                                                                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                          d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                                                </svg>
                                                                <span class="text-[11px] font-bold text-white uppercase tracking-wider">Simpan QR</span>
                                                            </div>
                                                        </button>
                                                        <p class="text-[11px] font-mono text-slate-500 mb-1">{{ $et->ticket_code }}</p>
                                                        <p class="text-sm font-bold text-slate-900 mb-2">{{ $item->ticket->name ?? 'Ticket' }}</p>

                                                        @php
                                                            $statusClass = 'bg-slate-100 text-slate-700';
                                                            $statusText = $et->status ?? 'active';
                                                            if ($statusText === 'active') {
                                                                $statusClass = 'bg-green-100 text-green-700';
                                                            } elseif ($statusText === 'used') {
                                                                $statusClass = 'bg-slate-200 text-slate-500 line-through';
                                                            } elseif ($statusText === 'cancelled') {
                                                                $statusClass = 'bg-red-100 text-red-700';
                                                            }
                                                        @endphp
                                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $statusClass }}">
                                                            {{ $statusText }}
                                                        </span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                @if($order->status === 'pending')
                                    <div class="mt-5 pt-4 border-t border-slate-100 flex flex-wrap items-center justify-between gap-3">
                                        <p class="text-sm text-amber-600 font-medium flex items-center gap-1.5">
                                            <span class="inline-block w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                                            Menunggu pembayaran
                                        </p>
                                        <div class="flex items-center gap-2">
                                            <button
                                                type="button"
                                                class="inline-flex items-center rounded-lg border border-red-300 px-4 py-2 text-sm font-bold text-red-600 hover:bg-red-50 transition-colors"
                                                onclick="confirmCancelOrder({{ $order->id }}, '{{ $order->order_number }}')"
                                            >
                                                Batalkan
                                            </button>
                                            @if($order->snap_token && $midtransEnabled)
                                            <button
                                                type="button"
                                                class="inline-flex items-center rounded-lg bg-primary px-6 py-2.5 text-sm font-bold text-white hover:bg-primary/90 transition-colors shadow-md hover:shadow-lg"
                                                onclick="payPendingOrder('{{ $order->snap_token }}', {{ $order->id }})"
                                            >
                                                Bayar Sekarang
                                            </button>
                                            @endif
                                        </div>
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

            async function confirmCancelOrder(orderId, orderNumber) {
                const ok = await evModal.confirm({
                    title: 'Batalkan Order?',
                    message: `Order ${orderNumber} akan dibatalkan.\nStok tiket akan dikembalikan dan order tidak bisa dipulihkan.`,
                    confirmText: 'Ya, Batalkan',
                    cancelText: 'Kembali',
                    danger: true,
                });
                if (!ok) return;

                try {
                    const res = await fetch(`/attendee/orders/${orderId}/cancel`, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                    });
                    const data = await res.json();
                    if (!res.ok) throw new Error(data.message || 'Gagal membatalkan order.');
                    window.location.reload();
                } catch (err) {
                    await evModal.alert({
                        title: 'Terjadi Kesalahan',
                        message: err.message || 'Terjadi kesalahan, coba lagi.',
                        icon: 'danger',
                    });
                }
            }

            function payPendingOrder(snapToken, orderId) {
                // Redirect ke halaman checkout khusus (embedded Snap, tidak popup)
                window.location.href = `/attendee/checkout/${orderId}`;
            }
        </script>
    @endif

    {{-- ─── Download QR sebagai PNG ──────────────────────────────────── --}}
    <script>
        function downloadQR(btn, ticketCode, eventLabel) {
            const svg = btn.querySelector('svg');
            if (!svg) return;

            const RESOLUTION = 600;
            const svgData = new XMLSerializer().serializeToString(svg);
            const blob = new Blob([svgData], { type: 'image/svg+xml;charset=utf-8' });
            const url = URL.createObjectURL(blob);

            const img = new Image();
            img.onload = function () {
                const canvas = document.createElement('canvas');
                canvas.width = RESOLUTION;
                canvas.height = RESOLUTION;
                const ctx = canvas.getContext('2d');
                ctx.fillStyle = '#ffffff';
                ctx.fillRect(0, 0, RESOLUTION, RESOLUTION);
                ctx.drawImage(img, 0, 0, RESOLUTION, RESOLUTION);
                URL.revokeObjectURL(url);

                canvas.toBlob(function (pngBlob) {
                    const safeLabel = (eventLabel || 'ticket').replace(/[^\w\-]+/g, '_');
                    const link = document.createElement('a');
                    link.href = URL.createObjectURL(pngBlob);
                    link.download = `${safeLabel}-${ticketCode}.png`;
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                    URL.revokeObjectURL(link.href);
                }, 'image/png');
            };
            img.onerror = function () {
                URL.revokeObjectURL(url);
                evModal.alert({
                    title: 'Gagal Menyimpan QR',
                    message: 'Tidak dapat memproses gambar. Silakan coba lagi.',
                    icon: 'danger',
                });
            };
            img.src = url;
        }
    </script>

    {{-- ─── Success animation + scroll ke tiket baru ─────────────────── --}}
    <div id="payment-success-overlay"
         class="fixed inset-0 z-[10000] flex items-center justify-center pointer-events-none opacity-0 transition-opacity duration-500"
         style="background:rgba(15,23,42,.55); backdrop-filter:blur(6px);">
        <div class="bg-white rounded-3xl shadow-2xl px-10 py-8 flex flex-col items-center gap-3 max-w-sm">
            <div class="success-check-wrap">
                <svg class="success-check" viewBox="0 0 52 52">
                    <circle class="success-check__circle" cx="26" cy="26" r="25" fill="none"/>
                    <path class="success-check__check" fill="none" d="M14 27l7 7 16-16"/>
                </svg>
            </div>
            <h3 class="text-xl font-black text-slate-900 mt-1">Pembayaran Berhasil!</h3>
            <p class="text-sm text-slate-500 text-center">Tiket Anda siap. Mengarahkan ke daftar tiket...</p>
        </div>
    </div>

    <style>
        /* ─── Animated checkmark ─── */
        .success-check-wrap { width: 96px; height: 96px; }
        .success-check { width: 100%; height: 100%; border-radius: 50%; display: block;
            stroke-width: 4; stroke: #22c55e; stroke-miterlimit: 10;
            box-shadow: inset 0 0 0 #22c55e;
            animation: checkFill .5s ease-in-out .4s forwards, checkScale .3s ease-in-out .9s both; }
        .success-check__circle { stroke-dasharray: 166; stroke-dashoffset: 166;
            stroke-width: 3; stroke-miterlimit: 10; stroke: #22c55e; fill: none;
            animation: checkStroke .7s cubic-bezier(.65,0,.45,1) forwards; }
        .success-check__check { transform-origin: 50% 50%; stroke-dasharray: 48; stroke-dashoffset: 48;
            animation: checkStroke .4s cubic-bezier(.65,0,.45,1) .6s forwards; }
        @keyframes checkStroke { to { stroke-dashoffset: 0; } }
        @keyframes checkScale { 0%,100% { transform: none; } 50% { transform: scale3d(1.1,1.1,1); } }
        @keyframes checkFill   { to { box-shadow: inset 0 0 0 50px #dcfce7; } }

        /* ─── Highlight tiket baru ─── */
        @keyframes orderHighlight {
            0%   { box-shadow: 0 0 0 0 rgba(34, 197, 94, .55); transform: scale(1); }
            50%  { box-shadow: 0 0 0 18px rgba(34, 197, 94, 0); transform: scale(1.01); }
            100% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0);   transform: scale(1); }
        }
        .order-card.is-new {
            animation: orderHighlight 1.8s ease-out 2 forwards;
        }
    </style>

    <script>
        (function () {
            const params  = new URLSearchParams(window.location.search);
            if (params.get('payment') !== 'success') return;

            const orderId  = params.get('order');
            const overlay  = document.getElementById('payment-success-overlay');

            // Fade-in overlay
            requestAnimationFrame(() => { overlay.style.opacity = '1'; });

            // Setelah ~1.6s, fade overlay, scroll & highlight tiket baru
            setTimeout(() => {
                overlay.style.opacity = '0';
                setTimeout(() => overlay.remove(), 500);

                const target = orderId
                    ? document.querySelector(`[data-order-id="${orderId}"]`)
                    : document.querySelector('.order-card');

                if (target) {
                    target.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    target.classList.add('is-new');
                }

                // Bersihkan URL biar tidak retrigger saat refresh
                const cleanUrl = window.location.pathname;
                window.history.replaceState({}, '', cleanUrl);
            }, 1600);
        })();
    </script>

    <x-ev-modal />
</body>
</html>
