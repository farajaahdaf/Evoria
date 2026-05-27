<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Checkout — Evoria</title>
    <link rel="icon" type="image/png" href="{{ asset('images/favicon-icon.png') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Plus Jakarta Sans', 'sans-serif'] },
                    colors: { primary: '#2563EB' },
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }

        /* Snap embed container styling */
        #snap-container iframe {
            width: 100% !important;
            border: none !important;
            border-radius: 16px;
        }
    </style>
</head>
<body class="bg-slate-100 min-h-screen">

    {{-- Header --}}
    <header class="bg-white border-b border-slate-200 sticky top-0 z-40">
        <div class="max-w-4xl mx-auto px-4 h-16 flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-2">
                <x-application-logo class="h-8 w-auto" />
            </a>
            <span class="text-sm text-slate-500 font-medium">Pembayaran Aman</span>
        </div>
    </header>

    <main class="max-w-4xl mx-auto px-4 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-6 items-start">

            {{-- Kolom kiri: info order + countdown --}}
            <div class="lg:col-span-2 space-y-4">

                {{-- Order summary --}}
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
                    <h2 class="text-base font-bold text-slate-700 mb-4">Ringkasan Order</h2>

                    @foreach($order->orderItems as $item)
                        @php $event = $item->ticket?->event; @endphp
                        <div class="flex items-start gap-3 pb-4 border-b border-slate-100 last:border-0 last:pb-0 mb-4 last:mb-0">
                            <div class="w-10 h-10 bg-primary/10 rounded-xl flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-bold text-slate-900 text-sm leading-tight truncate">
                                    {{ $event?->title ?? 'Event' }}
                                </p>
                                <p class="text-slate-500 text-xs mt-0.5">
                                    {{ $item->ticket?->name }} &times; {{ $item->quantity }}
                                </p>
                            </div>
                            <p class="font-bold text-slate-900 text-sm shrink-0">
                                Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                            </p>
                        </div>
                    @endforeach

                    <div class="mt-4 pt-4 border-t border-slate-200 flex justify-between items-center">
                        <span class="text-sm font-semibold text-slate-600">Total</span>
                        <span class="text-xl font-black text-primary">
                            Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                        </span>
                    </div>
                </div>

                {{-- Order number --}}
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm px-5 py-4">
                    <p class="text-xs text-slate-400 font-semibold uppercase tracking-wider mb-1">No. Order</p>
                    <p class="font-bold text-slate-800 text-sm tracking-wide">{{ $order->order_number }}</p>
                </div>

                {{-- Countdown timer (real deadline dari backend) --}}
                <div id="timer-card"
                     class="bg-amber-50 border border-amber-200 rounded-2xl px-5 py-4 transition-colors duration-500">
                    <div class="flex items-center gap-2 mb-1">
                        <svg id="timer-icon" class="w-4 h-4 text-amber-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-xs font-semibold text-amber-700 uppercase tracking-wide">Selesaikan dalam</p>
                    </div>
                    <p id="timer-display"
                       class="text-3xl font-black text-amber-800 tabular-nums tracking-tight">
                        --:--:--
                    </p>
                    <p class="text-xs text-amber-600 mt-1">
                        Selesaikan pembayaran sebelum batas waktu habis.
                        Order otomatis dibatalkan dan stok dikembalikan jika tidak dibayar.
                    </p>
                </div>

                {{-- Batalkan --}}
                <button id="btn-cancel"
                        onclick="confirmCancel()"
                        class="w-full py-3 rounded-xl border border-red-300 text-red-600 text-sm font-bold
                               hover:bg-red-50 transition-colors">
                    Batalkan Order
                </button>

            </div>

            {{-- Kolom kanan: Snap embed --}}
            <div class="lg:col-span-3">
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-100 flex items-center gap-2">
                        <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-sm font-semibold text-slate-600">Pembayaran aman via Midtrans</span>
                    </div>
                    <div id="snap-container" class="p-2 min-h-[500px]">
                        <div id="snap-loading"
                             class="flex flex-col items-center justify-center h-64 gap-3 text-slate-400">
                            <svg class="w-8 h-8 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor"
                                      d="M4 12a8 8 0 018-8v8H4z"/>
                            </svg>
                            <p class="text-sm font-medium">Memuat metode pembayaran...</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </main>

    {{-- Midtrans Snap.js --}}
    <script src="{{ $midtransSnapJsUrl }}"
            data-client-key="{{ $midtransClientKey }}"></script>

    @php
        $firstEvent   = $order->orderItems->first()?->ticket?->event;
        $eventUrl     = $firstEvent ? route('events.show', $firstEvent->slug) : route('home');
        $eventUrlError = $eventUrl . '?payment=error';
    @endphp
    <script>
        // ── Snap Embedded ──────────────────────────────────────────────────────
        window.addEventListener('load', function () {
            const snapToken   = '{{ $order->snap_token }}';
            const orderId     = {{ $order->id }};
            const orderNumber = '{{ $order->order_number }}';
            const csrf        = document.querySelector('meta[name="csrf-token"]').content;
            const eventUrl    = '{{ $eventUrl }}';
            const eventUrlErr = '{{ $eventUrlError }}';

            let _pending = false;

            document.getElementById('snap-loading')?.remove();

            snap.embed(snapToken, {
                embedId: 'snap-container',
                onSuccess: async function () {
                    await syncOrder(orderId, csrf);
                    window.location.href = '{{ route('attendee.dashboard') }}?payment=success';
                },
                onPending: async function () {
                    _pending = true;
                    await syncOrder(orderId, csrf);
                    window.location.href = '{{ route('attendee.dashboard') }}?payment=pending';
                },
                onError: async function () {
                    await cancelOrder(orderId, csrf);
                    window.location.href = eventUrlErr;
                },
                onClose: async function () {
                    if (!_pending) {
                        await cancelOrder(orderId, csrf);
                        window.location.href = eventUrl;
                    } else {
                        window.location.href = '{{ route('attendee.dashboard') }}';
                    }
                },
            });
        });

        // ── Countdown timer ────────────────────────────────────────────────────
        (function () {
            const expiresAt   = new Date('{{ $paymentExpiresAt }}');
            const display     = document.getElementById('timer-display');
            const card        = document.getElementById('timer-card');
            const icon        = document.getElementById('timer-icon');

            function tick() {
                const now  = new Date();
                const diff = Math.max(0, Math.floor((expiresAt - now) / 1000));

                const hh  = String(Math.floor(diff / 3600)).padStart(2, '0');
                const mm  = String(Math.floor((diff % 3600) / 60)).padStart(2, '0');
                const ss  = String(diff % 60).padStart(2, '0');
                display.textContent = hh + ':' + mm + ':' + ss;

                // Warna berubah sesuai urgency
                if (diff <= 0) {
                    card.className    = card.className.replace(/bg-\S+|border-\S+/g, '').trim()
                                        + ' bg-red-100 border border-red-400';
                    display.className = 'text-3xl font-black text-red-700 tabular-nums tracking-tight';
                    icon.className    = icon.className.replace('text-amber-500', 'text-red-500');
                    display.textContent = '00:00:00';
                    // Auto cancel dan redirect
                    const csrf    = document.querySelector('meta[name="csrf-token"]').content;
                    const oid     = {{ $order->id }};
                    cancelOrder(oid, csrf).finally(() => {
                        window.location.href = '{{ $eventUrl }}?payment=expired';
                    });
                    return; // stop
                } else if (diff <= 300) {
                    // ≤ 5 menit → merah
                    card.style.cssText    = 'background:#fef2f2;border-color:#fca5a5';
                    display.style.color   = '#b91c1c';
                    icon.style.color      = '#ef4444';
                } else if (diff <= 600) {
                    // ≤ 10 menit → oranye
                    card.style.cssText    = 'background:#fff7ed;border-color:#fdba74';
                    display.style.color   = '#c2410c';
                    icon.style.color      = '#f97316';
                }

                setTimeout(tick, 1000);
            }

            tick();
        })();

        // ── Helpers ────────────────────────────────────────────────────────────
        async function syncOrder(orderId, csrf) {
            try {
                await fetch(`/attendee/orders/${orderId}/refresh-status`, {
                    method: 'POST',
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
                });
            } catch (e) { console.warn(e); }
        }

        async function cancelOrder(orderId, csrf) {
            try {
                await fetch(`/attendee/orders/${orderId}/cancel`, {
                    method: 'POST',
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
                });
            } catch (e) { console.warn(e); }
        }

        async function confirmCancel() {
            const ok = await evModal.confirm({
                title: 'Batalkan Order?',
                message: 'Stok tiket akan dikembalikan dan order tidak bisa dipulihkan.',
                confirmText: 'Ya, Batalkan',
                cancelText: 'Kembali',
                danger: true,
            });
            if (!ok) return;

            const csrf    = document.querySelector('meta[name="csrf-token"]').content;
            const orderId = {{ $order->id }};

            document.getElementById('btn-cancel').disabled = true;
            document.getElementById('btn-cancel').textContent = 'Membatalkan...';

            await cancelOrder(orderId, csrf);
            window.location.href = '{{ $eventUrl }}';
        }
    </script>

    <x-ev-modal />
</body>
</html>
