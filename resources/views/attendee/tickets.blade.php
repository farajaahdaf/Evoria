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
<body class="bg-[#F4F6F9] min-h-screen text-slate-900">
    <x-attendee-main-header />

    <main class="max-w-[1200px] mx-auto px-6 py-10 space-y-8">
        <section class="bg-white rounded-2xl border border-slate-100 p-6 md:p-8 shadow-sm">
            <p class="text-[12px] font-extrabold tracking-widest text-primary uppercase">Attendee Area</p>
            <h1 class="text-3xl md:text-4xl font-black text-slate-900 mt-2">Tiket Saya</h1>
            <p class="text-slate-500 mt-2">Semua tiket yang sudah kamu beli akan tampil di halaman ini.</p>
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

        <section class="space-y-5">
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
                    @endphp
                    <article class="bg-white rounded-2xl border border-slate-100 overflow-hidden shadow-sm">
                        <div class="flex flex-col lg:flex-row">
                            <div class="w-full lg:w-[320px] h-52 lg:h-auto bg-slate-200 relative">
                                <img src="{{ $bannerUrl }}" alt="{{ $event->title ?? 'Banner Event' }}" class="w-full h-full object-cover">
                                <div class="absolute top-4 left-4 bg-white text-slate-900 text-xs font-bold px-3 py-1.5 rounded-md shadow-sm">
                                    {{ $item->ticket->name }}
                                </div>
                            </div>

                            <div class="flex-1 p-6 md:p-7">
                                <div class="flex items-start justify-between gap-4 mb-3">
                                    <h2 class="text-xl md:text-2xl font-black text-slate-900 leading-tight">
                                        {{ $event->title ?? 'Event Tidak Tersedia' }}
                                    </h2>
                                    <span @class([
                                        'px-3 py-1 rounded-full text-xs font-bold uppercase',
                                        'bg-green-100 text-green-700' => $order->status === 'paid',
                                        'bg-amber-100 text-amber-700' => $order->status === 'pending',
                                        'bg-red-100 text-red-700' => in_array($order->status, ['failed', 'cancelled', 'refunded'], true),
                                    ])>
                                        {{ $order->status }}
                                    </span>
                                </div>

                                <div class="grid sm:grid-cols-2 gap-3 text-sm text-slate-600">
                                    <p class="flex items-center gap-1.5">
                                        <span class="material-symbols-outlined text-[18px]">calendar_today</span>
                                        {{ $event ? \Carbon\Carbon::parse($event->start_time)->translatedFormat('d M Y, H:i') : '-' }}
                                    </p>
                                    <p class="flex items-center gap-1.5">
                                        <span class="material-symbols-outlined text-[18px]">location_on</span>
                                        {{ $event->location_name ?? '-' }}
                                    </p>
                                </div>

                                <div class="mt-5 pt-4 border-t border-slate-100 flex flex-wrap items-center justify-between gap-3">
                                    <p class="text-sm">
                                        <span class="text-slate-500">Order:</span>
                                        <span class="font-bold text-slate-900">{{ $order->order_number }}</span>
                                    </p>
                                    <p class="text-sm">
                                        <span class="text-slate-500">Jumlah:</span>
                                        <span class="font-bold text-slate-900">{{ $item->quantity }} tiket</span>
                                    </p>
                                    <p class="text-sm">
                                        <span class="text-slate-500">Total:</span>
                                        <span class="font-black text-slate-900">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                                    </p>
                                </div>

                                @if($order->status === 'pending' && $order->snap_token && $midtransEnabled)
                                    <div class="mt-4 flex flex-wrap items-center gap-3">
                                        <button
                                            type="button"
                                            class="inline-flex items-center rounded-lg bg-primary px-4 py-2.5 text-sm font-bold text-white hover:bg-primary/90 transition-colors"
                                            onclick="payPendingOrder('{{ $order->snap_token }}', {{ $order->id }})"
                                        >
                                            Lanjutkan Pembayaran
                                        </button>
                                        <p class="text-xs text-slate-500">Order ini masih menunggu pembayaran dari Midtrans.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </article>
                @endforeach
            @empty
                <div class="bg-white rounded-2xl border border-slate-100 p-10 text-center shadow-sm">
                    <div class="w-16 h-16 mx-auto rounded-full bg-slate-100 flex items-center justify-center mb-4">
                        <span class="material-symbols-outlined text-[32px] text-slate-400">confirmation_number</span>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900">Belum ada tiket</h3>
                    <p class="text-slate-500 mt-2">Kamu belum membeli tiket event apa pun.</p>
                    <a href="{{ route('home') }}" class="inline-block mt-5 px-6 py-3 rounded-lg bg-primary text-white font-bold hover:bg-primary/90 transition-colors">
                        Cari Event
                    </a>
                </div>
            @endforelse
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
