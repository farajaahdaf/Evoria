<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('success'))
                <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 text-center">
                        <p class="text-sm font-medium text-slate-500">Total Saldo</p>
                        <h3 class="mt-3 text-3xl font-black text-slate-900">Rp {{ number_format($availableBalance ?? 0, 0, ',', '.') }}</h3>
                    </div>

                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 text-center">
                        <p class="text-sm font-medium text-slate-500">Total Pendapatan</p>
                        <h3 class="mt-3 text-3xl font-black text-slate-900">Rp {{ number_format($totalRevenue ?? 0, 0, ',', '.') }}</h3>
                    </div>

                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 text-center">
                        <p class="text-sm font-medium text-slate-500">Tiket Terjual</p>
                        <h3 class="mt-3 text-3xl font-black text-slate-900">{{ number_format($totalTicketsSold ?? 0) }}</h3>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
                    <div class="flex items-center justify-between gap-3 mb-4">
                        <h3 class="text-xl font-bold text-slate-900">Withdraw</h3>
                    </div>

                    <form id="withdraw-form" action="{{ route('organizer.withdraw') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label for="amount" class="text-sm font-medium text-slate-700">Nominal Withdraw (Rp)</label>
                            <input
                                id="amount"
                                name="amount"
                                type="number"
                                min="1"
                                step="1"
                                value="{{ old('amount') }}"
                                class="mt-1 w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500"
                                placeholder="Contoh: 50000"
                                required
                            >
                            @error('amount')
                                <p class="mt-2 text-xs font-medium text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <button type="submit" class="w-full rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-bold text-white hover:bg-indigo-700 transition">
                            Withdraw Sekarang
                        </button>
                    </form>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                    <div class="p-6 border-b border-slate-100">
                        <h3 class="text-xl font-bold text-slate-900">Rincian Pemasukan per Event</h3>
                        <p class="mt-1 text-sm text-slate-500">Lihat event mana yang menghasilkan penjualan tiket dan berapa pemasukan yang didapat.</p>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50 border-b border-slate-100">
                                    <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Event</th>
                                    <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-right">Tiket Terjual</th>
                                    <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-right">Pendapatan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($eventSales as $sale)
                                    <tr class="hover:bg-slate-50 transition">
                                        <td class="p-4">
                                            <p class="font-bold text-slate-900">{{ $sale->title }}</p>
                                        </td>
                                        <td class="p-4 text-right text-sm font-medium text-slate-700">
                                            {{ number_format($sale->tickets_sold) }}
                                        </td>
                                        <td class="p-4 text-right text-sm font-bold text-slate-900">
                                            Rp {{ number_format($sale->revenue, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="p-8 text-center text-slate-500 italic">
                                            Belum ada tiket terjual untuk event Anda.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
                    <h3 class="text-xl font-bold text-slate-900 mb-4">Riwayat Withdraw</h3>
                    <div class="space-y-3">
                        @forelse($recentWithdrawals ?? [] as $withdrawal)
                            <div class="flex items-center justify-between rounded-xl border border-slate-100 px-3 py-3">
                                <div>
                                    <p class="text-sm font-semibold text-slate-900">Withdraw Berhasil</p>
                                    <p class="text-xs text-slate-500">{{ $withdrawal->created_at->format('d M Y H:i') }}</p>
                                </div>
                                <span class="text-sm font-bold text-slate-900">Rp {{ number_format($withdrawal->amount, 0, ',', '.') }}</span>
                            </div>
                        @empty
                            <p class="text-sm text-slate-500 italic">Belum ada riwayat withdraw.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
