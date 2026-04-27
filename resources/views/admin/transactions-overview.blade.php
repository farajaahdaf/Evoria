<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
            {{ __('Transaction Overview') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                    <div class="max-w-2xl">
                        <p class="text-xs font-black text-purple-600 uppercase tracking-[0.25em]">Transaction Overview</p>
                        <h3 class="mt-3 text-3xl font-black text-gray-900">Ringkasan cepat status transaksi Evoria</h3>
                        <p class="mt-3 text-sm text-gray-500 leading-relaxed">
                            Halaman ini difokuskan untuk monitoring singkat. Gunakan halaman manajemen transaksi untuk melihat tabel lengkap dan tindak lanjut operasional.
                        </p>
                    </div>
                    <a href="{{ route('admin.transactions') }}" class="inline-flex items-center justify-center px-6 py-3 bg-gray-900 text-white text-sm font-extrabold rounded-2xl hover:bg-gray-800 transition-all shadow-lg">
                        Manage All Transactions
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Total</p>
                    <h4 class="text-4xl font-black text-gray-900">{{ number_format($totalTransactions) }}</h4>
                </div>
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-orange-100 bg-orange-50/40">
                    <p class="text-xs font-bold text-orange-500 uppercase tracking-widest mb-2">Pending</p>
                    <h4 class="text-4xl font-black text-orange-700">{{ number_format($pendingTransactionsCount) }}</h4>
                </div>
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-emerald-100 bg-emerald-50/40">
                    <p class="text-xs font-bold text-emerald-500 uppercase tracking-widest mb-2">Paid</p>
                    <h4 class="text-4xl font-black text-emerald-700">{{ number_format($paidTransactionsCount) }}</h4>
                </div>
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-red-100 bg-red-50/40">
                    <p class="text-xs font-bold text-red-500 uppercase tracking-widest mb-2">Failed / Cancelled</p>
                    <h4 class="text-4xl font-black text-red-700">{{ number_format($failedTransactionsCount) }}</h4>
                </div>
            </div>

            <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">Recent Transactions</h3>
                        <p class="text-sm text-gray-500 mt-1">Enam transaksi terbaru untuk pengecekan cepat.</p>
                    </div>
                    <a href="{{ route('admin.transactions') }}" class="text-sm font-bold text-purple-600 hover:text-purple-800 transition-colors">Open full table</a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                    @forelse($latestOrders as $order)
                    <div class="rounded-2xl border border-gray-100 bg-gray-50 p-5">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-xs font-black text-gray-400 uppercase tracking-widest">Order</p>
                                <p class="mt-2 text-sm font-black text-gray-900 break-all">{{ $order->order_number }}</p>
                            </div>
                            <span class="px-2.5 py-1 text-[10px] font-black rounded-full uppercase tracking-widest
                                {{ $order->status === 'paid' ? 'bg-emerald-100 text-emerald-700' : ($order->status === 'pending' ? 'bg-orange-100 text-orange-700' : 'bg-red-100 text-red-700') }}">
                                {{ str_replace('_', ' ', $order->status) }}
                            </span>
                        </div>
                        <div class="mt-5 space-y-2 text-sm">
                            <div class="flex justify-between gap-4">
                                <span class="text-gray-400 font-semibold">Buyer</span>
                                <span class="text-gray-700 font-bold text-right">{{ $order->user->name }}</span>
                            </div>
                            <div class="flex justify-between gap-4">
                                <span class="text-gray-400 font-semibold">Amount</span>
                                <span class="text-gray-900 font-black text-right">IDR {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between gap-4">
                                <span class="text-gray-400 font-semibold">Date</span>
                                <span class="text-gray-700 font-bold text-right">{{ $order->created_at->format('d M Y, H:i') }}</span>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="md:col-span-2 xl:col-span-3 p-10 bg-gray-50 rounded-3xl border-2 border-dashed border-gray-200 text-center text-gray-400 font-bold italic">
                        Belum ada transaksi untuk ditampilkan.
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
