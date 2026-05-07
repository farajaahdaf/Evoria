<x-app-layout>
    @php
        $status = auth()->user()->organizerProfile?->status ?? 'pending';
        $isVerified = $status === 'verified';
    @endphp

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-700 px-6 py-4 rounded-2xl shadow-sm flex items-center gap-3">
                    <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span class="font-semibold">{{ session('success') }}</span>
                </div>
            @endif

            <!-- Hero -->
            <div class="relative bg-gray-900 rounded-3xl overflow-hidden shadow-2xl">
                <div class="absolute inset-0 bg-gradient-to-r from-indigo-900/60 to-transparent"></div>
                <div class="relative p-10 flex items-center justify-between">
                    <div class="max-w-lg space-y-4">
                        <div class="flex items-center gap-3">
                            <p class="text-xs font-black text-indigo-200 uppercase tracking-[0.25em]">Organizer Dashboard</p>
                            <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest {{ $isVerified ? 'bg-green-400 text-green-900' : 'bg-yellow-400 text-yellow-900' }}">
                                {{ $isVerified ? 'Verified' : 'Pending Verification' }}
                            </span>
                        </div>
                        <h3 class="text-4xl font-extrabold text-white tracking-tight">Welcome back, {{ auth()->user()->name }}!</h3>
                        <p class="text-gray-300 text-lg leading-relaxed">
                            Kelola event, pantau penjualan tiket, dan kembangkan audiens Anda.
                        </p>
                        <div class="flex flex-wrap gap-3 pt-2">
                            @if($isVerified)
                                <a href="{{ route('organizer.events.create') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-white text-gray-900 font-bold rounded-xl hover:bg-gray-100 transition-all shadow-lg text-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                    Buat Event
                                </a>
                            @endif
                            <a href="{{ route('organizer.events.index') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-gray-800 text-white font-bold rounded-xl border border-gray-700 hover:bg-gray-700 transition-all shadow-lg text-sm">
                                Lihat Semua Event
                            </a>
                        </div>
                    </div>
                    <div class="hidden md:flex pr-10 opacity-80">
                        <span class="material-symbols-outlined text-white" style="font-size:130px;">event_note</span>
                    </div>
                </div>
            </div>

            <!-- Stat Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <a href="{{ route('organizer.events.index') }}" class="block bg-white rounded-3xl p-6 shadow-sm border border-gray-100 relative overflow-hidden group transition hover:-translate-y-1 hover:shadow-md">
                    <div class="flex justify-between items-start mb-4">
                        <div class="p-3 bg-gray-50 rounded-2xl text-gray-900 group-hover:bg-indigo-600 group-hover:text-white transition-all duration-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        </div>
                    </div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Total Events</p>
                    <h4 class="text-3xl font-black text-gray-900">{{ number_format($eventsCount ?? 0) }}</h4>
                </a>

                <a href="{{ route('organizer.events.index') }}" class="block bg-white rounded-3xl p-6 shadow-sm border border-gray-100 relative overflow-hidden group transition hover:-translate-y-1 hover:shadow-md">
                    <div class="flex justify-between items-start mb-4">
                        <div class="p-3 bg-green-50 rounded-2xl text-green-700 group-hover:bg-green-600 group-hover:text-white transition-all duration-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                    </div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Published Events</p>
                    <h4 class="text-3xl font-black text-gray-900">{{ number_format($publishedCount ?? 0) }}</h4>
                </a>

                <div class="block bg-white rounded-3xl p-6 shadow-sm border border-gray-100 relative overflow-hidden group transition hover:-translate-y-1 hover:shadow-md">
                    <div class="flex justify-between items-start mb-4">
                        <div class="p-3 bg-amber-50 rounded-2xl text-amber-700 group-hover:bg-amber-500 group-hover:text-white transition-all duration-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path></svg>
                        </div>
                    </div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Tiket Terjual</p>
                    <h4 class="text-3xl font-black text-gray-900">{{ number_format($totalTicketsSold ?? 0) }}</h4>
                </div>

                <a href="{{ route('organizer.balance') }}" class="block bg-white rounded-3xl p-6 shadow-sm border border-gray-100 relative overflow-hidden group transition hover:-translate-y-1 hover:shadow-md">
                    <div class="flex justify-between items-start mb-4">
                        <div class="p-3 bg-emerald-50 rounded-2xl text-emerald-700 group-hover:bg-emerald-600 group-hover:text-white transition-all duration-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                    </div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Total Revenue</p>
                    <h4 class="text-2xl font-black text-gray-900">Rp {{ number_format($totalRevenue ?? 0, 0, ',', '.') }}</h4>
                </a>
            </div>

            <!-- Feature Buttons -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @if($isVerified)
                <a href="{{ route('organizer.events.create') }}"
                   class="group flex flex-col items-center gap-3 p-6 bg-white rounded-2xl border border-slate-100 shadow-sm hover:border-indigo-400 hover:shadow-md transition">
                    <div class="w-14 h-14 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center group-hover:bg-indigo-600 group-hover:text-white transition-all">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    </div>
                    <span class="text-sm font-bold text-slate-800 group-hover:text-indigo-600 transition text-center">Buat Event Baru</span>
                </a>
                @endif

                <a href="{{ route('organizer.events.index') }}"
                   class="group flex flex-col items-center gap-3 p-6 bg-white rounded-2xl border border-slate-100 shadow-sm hover:border-blue-400 hover:shadow-md transition">
                    <div class="w-14 h-14 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white transition-all">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                    </div>
                    <span class="text-sm font-bold text-slate-800 group-hover:text-blue-600 transition text-center">Event Saya</span>
                </a>

                <a href="{{ route('organizer.events.index') }}"
                   class="group flex flex-col items-center gap-3 p-6 bg-white rounded-2xl border border-slate-100 shadow-sm hover:border-emerald-400 hover:shadow-md transition">
                    <div class="w-14 h-14 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center group-hover:bg-emerald-600 group-hover:text-white transition-all">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 3.5V16M4 6h16M4 10h16"></path></svg>
                    </div>
                    <span class="text-sm font-bold text-slate-800 group-hover:text-emerald-600 transition text-center">Scan Check-in</span>
                </a>

                <a href="{{ route('organizer.balance') }}"
                   class="group flex flex-col items-center gap-3 p-6 bg-white rounded-2xl border border-slate-100 shadow-sm hover:border-violet-400 hover:shadow-md transition">
                    <div class="w-14 h-14 bg-violet-50 text-violet-600 rounded-2xl flex items-center justify-center group-hover:bg-violet-600 group-hover:text-white transition-all">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                    </div>
                    <span class="text-sm font-bold text-slate-800 group-hover:text-violet-600 transition text-center">Saldo & Withdraw</span>
                </a>
            </div>

            <!-- Recent Events + Quick Info -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Recent Events -->
                <div class="lg:col-span-2 bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
                    <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                        <h4 class="text-lg font-bold text-slate-900">Event Terbaru</h4>
                        <a href="{{ route('organizer.events.index') }}" class="text-primary hover:text-blue-700 text-sm font-semibold transition">Lihat Semua →</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50 border-b border-slate-100">
                                    <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Event</th>
                                    <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Tanggal</th>
                                    <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Status</th>
                                    <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($recentEvents ?? [] as $event)
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="p-4">
                                        <p class="font-bold text-slate-900 line-clamp-1" title="{{ $event->title }}">{{ $event->title }}</p>
                                    </td>
                                    <td class="p-4">
                                        <p class="text-sm text-slate-600">{{ $event->start_time->format('d M Y') }}</p>
                                    </td>
                                    <td class="p-4 text-center">
                                        @php
                                            $st = $event->status;
                                            $badge = match($st) {
                                                'published'      => 'bg-green-100 text-green-700',
                                                'pending_review' => 'bg-yellow-100 text-yellow-700',
                                                'draft'          => 'bg-gray-100 text-gray-600',
                                                'rejected'       => 'bg-red-100 text-red-700',
                                                default          => 'bg-slate-100 text-slate-600',
                                            };
                                        @endphp
                                        <span class="px-2.5 py-1 rounded-md text-[11px] font-bold uppercase tracking-wider {{ $badge }}">
                                            {{ str_replace('_', ' ', $st) }}
                                        </span>
                                    </td>
                                    <td class="p-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('organizer.events.attendees', $event->id) }}"
                                               class="px-3 py-1.5 bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white rounded-lg text-xs font-bold transition">
                                                Attendees
                                            </a>
                                            <a href="{{ route('organizer.events.checkin', $event->id) }}"
                                               class="px-3 py-1.5 bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white rounded-lg text-xs font-bold transition">
                                                Check-in
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="p-10 text-center text-slate-400 text-sm">
                                        <div class="flex flex-col items-center gap-2">
                                            <span class="material-symbols-outlined text-slate-300" style="font-size:48px;">event_busy</span>
                                            Belum ada event. Mulai buat event pertama Anda!
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Account Info -->
                <div class="space-y-4">
                    <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100">
                        <h4 class="text-lg font-bold text-slate-900 mb-5">Info Akun</h4>
                        <div class="space-y-4">
                            @php $user = auth()->user(); @endphp
                            <div class="flex items-center gap-4">
                                <img src="{{ $user->profile_photo_path ? \Illuminate\Support\Facades\Storage::url($user->profile_photo_path) : 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=0f172a&color=fff&size=128' }}"
                                     alt="{{ $user->name }}" class="w-14 h-14 rounded-2xl object-cover border border-slate-100 shadow">
                                <div>
                                    <p class="font-bold text-slate-900">{{ $user->name }}</p>
                                    <p class="text-sm text-slate-500">{{ $user->email }}</p>
                                </div>
                            </div>
                            <div class="bg-slate-50 rounded-2xl p-4 space-y-2">
                                <div class="flex justify-between text-sm">
                                    <span class="text-slate-500 font-medium">Status</span>
                                    <span class="font-bold {{ $isVerified ? 'text-green-600' : 'text-yellow-600' }}">{{ $isVerified ? 'Verified' : 'Pending' }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-slate-500 font-medium">Saldo</span>
                                    <span class="font-bold text-slate-900">Rp {{ number_format($user->balance ?? 0, 0, ',', '.') }}</span>
                                </div>
                            </div>
                            <a href="{{ route('organizer.balance') }}" class="w-full flex items-center justify-center gap-2 py-3 bg-gradient-to-r from-indigo-600 to-violet-600 text-white font-bold rounded-2xl text-sm hover:opacity-90 transition shadow-md">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                                Kelola Saldo
                            </a>
                        </div>
                    </div>

                    <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100">
                        <h4 class="text-lg font-bold text-slate-900 mb-4">Fitur Lainnya</h4>
                        <div class="space-y-2">
                            <a href="{{ route('organizer.events.index') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-slate-50 transition group">
                                <div class="w-9 h-9 bg-slate-100 rounded-xl flex items-center justify-center text-slate-500 group-hover:bg-indigo-600 group-hover:text-white transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </div>
                                <span class="text-sm font-semibold text-slate-700">Kelola Event</span>
                            </a>
                            <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-slate-50 transition group">
                                <div class="w-9 h-9 bg-slate-100 rounded-xl flex items-center justify-center text-slate-500 group-hover:bg-blue-600 group-hover:text-white transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                </div>
                                <span class="text-sm font-semibold text-slate-700">Edit Profil</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
