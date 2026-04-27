<x-app-layout>
    <div class="py-12 bg-[#F8FAFC] min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('success'))
                <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- 3 Top Cards -->
                <div class="lg:col-span-2 grid grid-cols-1 md:grid-cols-3 gap-6">
                    
                    <!-- Card 1: Total Saldo -->
                    <div class="bg-white rounded-2xl p-6 shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] border-b-[5px] border-[#3B82F6] flex flex-col items-center justify-between relative h-full">
                        <p class="text-xs font-semibold text-slate-700 mb-4 tracking-wide">Total Saldo</p>
                        <div class="flex items-center gap-3 mb-6">
                            <!-- Custom Wallet Icon -->
                            <div class="w-12 h-12 bg-[#E0E7FF] text-[#1E3A8A] rounded-lg border-[2px] border-[#1E3A8A] flex items-center justify-center relative flex-shrink-0">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                                <!-- Small lock badge -->
                                <div class="absolute -bottom-1 -left-1 w-5 h-5 bg-[#3B82F6] rounded-full border-2 border-white flex items-center justify-center text-white">
                                    <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path></svg>
                                </div>
                            </div>
                            <h3 class="text-3xl font-black text-[#1E3A8A]">Rp {{ number_format($availableBalance ?? 0, 0, ',', '.') }}</h3>
                        </div>
                        <p class="text-sm font-medium text-slate-600">Segera cairkan dana Anda!</p>
                    </div>

                    <!-- Card 2: Total Pendapatan -->
                    <div class="bg-white rounded-2xl p-6 shadow-[0_2px_10px_-3px_rgba(16,185,129,0.1)] border-b-[5px] border-[#10B981] flex flex-col items-center justify-between relative h-full">
                        <p class="text-xs font-semibold text-slate-700 mb-4 tracking-wide">Total Pendapatan</p>
                        <h3 class="text-3xl font-black text-slate-900 mb-6">Rp {{ number_format($totalRevenue ?? 0, 0, ',', '.') }}</h3>
                        <div class="flex items-center gap-1.5 text-[#10B981] font-bold">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                            <span class="text-sm">+12% minggu ini</span>
                        </div>
                    </div>

                    <!-- Card 3: Tiket Terjual -->
                    <div class="bg-white rounded-2xl p-6 shadow-[0_2px_10px_-3px_rgba(59,130,246,0.1)] border-b-[5px] border-[#3B82F6] flex flex-col items-center justify-between relative h-full">
                        <p class="text-xs font-semibold text-slate-700 mb-4 tracking-wide">Tiket Terjual</p>
                        <div class="flex items-center gap-4 mb-6">
                            <!-- Custom Ticket Icon -->
                            <div class="w-12 h-12 text-[#1E3A8A] flex flex-col items-center justify-center relative flex-shrink-0">
                                <svg class="w-10 h-10 transform -rotate-12" fill="#E0E7FF" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path></svg>
                                <svg class="w-10 h-10 absolute bottom-0 right-0 transform translate-y-2 translate-x-2 bg-white rounded shadow-sm" fill="#E0E7FF" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path></svg>
                            </div>
                            <h3 class="text-3xl font-black text-slate-900">{{ number_format($totalTicketsSold ?? 0) }}</h3>
                        </div>
                        <p class="text-sm font-medium text-slate-600">Sisa kuota: 98</p>
                    </div>

                </div>

                <!-- Withdraw Section -->
                <div class="bg-white rounded-2xl p-6 shadow-[0_2px_10px_-3px_rgba(0,0,0,0.05)] border border-slate-100 flex flex-col">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-bold text-slate-900">Withdraw</h3>
                        <span class="text-[11px] font-semibold text-slate-600 bg-slate-50 px-3 py-1 rounded-full border border-slate-200">Saldo tersedia: Rp {{ number_format($availableBalance ?? 0, 0, ',', '.') }}</span>
                    </div>

                    <form id="withdraw-form" action="{{ route('organizer.withdraw') }}" method="POST" class="flex-1 flex flex-col">
                        @csrf
                        <div class="flex items-end gap-4 mb-4">
                            <!-- Custom Wallet Icon -->
                            <div class="w-12 h-12 bg-[#E0E7FF] text-[#1E3A8A] rounded-lg border-[2px] border-[#1E3A8A] hidden sm:flex items-center justify-center relative flex-shrink-0 mb-[1px]">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                                <div class="absolute -bottom-1 -right-1 w-5 h-5 bg-[#3B82F6] rounded-full border-2 border-white flex items-center justify-center text-white">
                                    <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                </div>
                            </div>
                            
                            <div class="flex-1">
                                <label for="amount" class="block text-xs text-slate-600 mb-1 ml-1">Nominal Withdraw (Rp)</label>
                                <input
                                    id="amount"
                                    name="amount"
                                    type="number"
                                    min="1"
                                    step="1"
                                    value="{{ old('amount') }}"
                                    class="w-full rounded-xl border-slate-200 shadow-sm focus:border-[#4F46E5] focus:ring-[#4F46E5] text-sm py-2.5 px-4"
                                    placeholder="Contoh: 50000"
                                    required
                                >
                            </div>
                        </div>
                        @error('amount')
                            <p class="mb-4 text-xs font-medium text-red-600">{{ $message }}</p>
                        @enderror

                        <div class="mt-auto pt-2">
                            <button type="submit" class="w-full flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-[#5B4CFA] to-[#4A3EE0] px-4 py-3 text-sm font-bold text-white hover:opacity-90 transition shadow-md shadow-indigo-200">
                                Withdraw Sekarang
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Rincian Pemasukan per Event -->
                <div class="lg:col-span-2 bg-white rounded-2xl shadow-[0_2px_10px_-3px_rgba(0,0,0,0.05)] border border-slate-100 overflow-hidden">
                    <div class="p-6 border-b border-slate-50">
                        <h3 class="text-xl font-bold text-slate-900">Rincian Pemasukan per Event</h3>
                        <p class="mt-1 text-sm text-slate-500 font-medium">Lihat event mana yang menghasilkan penjualan tiket dan berapa pemasukan yang didapat.</p>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse min-w-[600px] mb-4">
                            <thead>
                                <tr class="bg-transparent border-b border-slate-100">
                                    <th class="py-4 px-6 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Event</th>
                                    <th class="py-4 px-6 text-[11px] font-bold text-slate-500 uppercase tracking-wider text-center">Status</th>
                                    <th class="py-4 px-6 text-[11px] font-bold text-slate-500 uppercase tracking-wider text-center">Tiket Terjual</th>
                                    <th class="py-4 px-6 text-[11px] font-bold text-slate-500 uppercase tracking-wider text-right">Pendapatan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($eventSales as $sale)
                                    <tr class="hover:bg-slate-50/70 transition-colors group">
                                        <td class="p-4 pl-6">
                                            <div class="flex items-center gap-4">
                                                <div class="w-10 h-10 bg-slate-200 rounded-lg overflow-hidden flex-shrink-0 border border-slate-100 shadow-sm">
                                                    <img src="{{ $sale->banner_path ? asset('storage/' . $sale->banner_path) : 'https://images.unsplash.com/photo-1514525253161-7a46d19cd819?ixlib=rb-4.0.3&auto=format&fit=crop&w=150&q=80' }}" alt="{{ $sale->title }}" class="w-full h-full object-cover">
                                                </div>
                                                <div>
                                                    <p class="font-bold text-sm text-slate-900 group-hover:text-[#4F46E5] transition">{{ $sale->title }}</p>
                                                    <p class="text-[11px] text-slate-500 font-medium mt-0.5">{{ $sale->start_time ? \Carbon\Carbon::parse($sale->start_time)->format('d M Y') : '-' }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="p-4 text-center">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold bg-[#E8F8F1] text-[#10B981] border border-[#A7F3D0]">
                                                Selesai
                                            </span>
                                        </td>
                                        <td class="p-4 text-center text-sm font-semibold text-slate-700">
                                            {{ number_format($sale->tickets_sold) }}
                                        </td>
                                        <td class="p-4 pr-6 text-right text-sm font-bold text-slate-900">
                                            Rp {{ number_format($sale->revenue, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="p-8 text-center text-slate-400 text-sm font-medium">
                                            <div class="flex flex-col items-center justify-center py-6">
                                                <svg class="w-12 h-12 text-slate-200 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                                Belum ada tiket terjual untuk event Anda.
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Riwayat Withdraw -->
                <div class="bg-white rounded-2xl p-6 shadow-[0_2px_10px_-3px_rgba(0,0,0,0.05)] border border-slate-100">
                    <h3 class="text-xl font-bold text-slate-900 mb-8">Riwayat Withdraw</h3>
                    
                    <div class="relative pl-4 space-y-8 border-l-[2px] border-slate-100 ml-2">
                        
                        @php $hasWithdrawals = false; @endphp
                        @forelse($recentWithdrawals ?? [] as $withdrawal)
                            @php $hasWithdrawals = true; @endphp
                            <div class="relative pl-6">
                                <!-- Dot -->
                                <span class="absolute -left-[27px] top-1 w-3 h-3 bg-[#5B4CFA] rounded-full border-[3px] border-white ring-[3px] ring-indigo-50 shadow-sm"></span>
                                
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="text-sm font-bold text-slate-900 mb-1">Withdraw Berhasil</p>
                                        <h4 class="text-2xl font-black text-slate-900 tracking-tight leading-none mb-1.5">Rp {{ number_format($withdrawal->amount, 0, ',', '.') }}</h4>
                                        <p class="text-xs font-semibold text-slate-500 mb-0.5">BCA - 12345678</p>
                                        <p class="text-[11px] font-medium text-slate-400">{{ $withdrawal->created_at->format('d M Y H:i') }}</p>
                                    </div>
                                    <div class="w-10 h-10 rounded-full bg-[#FAFAFA] border border-slate-200 flex items-center justify-center text-[#1E3A8A] flex-shrink-0 shadow-sm">
                                        <svg class="w-4 h-4 text-slate-600" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z"></path><path fill-rule="evenodd" d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z" clip-rule="evenodd"></path></svg>
                                    </div>
                                </div>
                            </div>
                        @empty
                        @endforelse

                        <!-- Empty State always showing at bottom as per design -->
                        <div class="relative pl-6 {{ !$hasWithdrawals ? '' : 'pt-2' }}">
                            <span class="absolute -left-[27px] top-1.5 w-3 h-3 bg-slate-300 rounded-full border-[3px] border-white"></span>
                            <div>
                                <p class="text-sm font-bold text-slate-500 mb-0.5">Empty State</p>
                                <p class="text-xs text-slate-400 font-medium">Penarikan sebelumnya akan muncul di sini.</p>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
