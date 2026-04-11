<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <!-- 1. Header Banner -->
            <div class="bg-gradient-to-r from-slate-800 to-slate-900 rounded-2xl p-8 shadow-xl text-white">
                <h3 class="text-3xl font-black mb-2">Admin Control Panel</h3>
                <p class="text-slate-300 text-lg">System overview and platform monitoring.</p>
            </div>

            <!-- 2. Stats Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Total Users -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-blue-50 flex items-center justify-center text-blue-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    </div>
                    <div>
                        <p class="text-sm text-slate-500 font-medium">Total Users</p>
                        <h4 class="text-2xl font-black text-slate-900">{{ number_format($totalUsers ?? 0) }}</h4>
                    </div>
                </div>

                <!-- Verified Organizers -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-green-50 flex items-center justify-center text-green-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path></svg>
                    </div>
                    <div>
                        <p class="text-sm text-slate-500 font-medium">Verified Organizers</p>
                        <h4 class="text-2xl font-black text-slate-900">{{ number_format($totalOrganizers ?? 0) }}</h4>
                    </div>
                </div>

                <!-- Published Events -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                    <div>
                        <p class="text-sm text-slate-500 font-medium">Published Events</p>
                        <h4 class="text-2xl font-black text-slate-900">{{ number_format($totalEvents ?? 0) }}</h4>
                    </div>
                </div>

                <!-- Total Revenue -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-emerald-50 flex items-center justify-center text-emerald-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <p class="text-sm text-slate-500 font-medium">Total Revenue</p>
                        <h4 class="text-2xl font-black text-slate-900">Rp {{ number_format($totalRevenue ?? 0, 0, ',', '.') }}</h4>
                    </div>
                </div>
            </div>

            <!-- 3. Action Cards (Queue) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Organizers Queue -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 flex flex-col justify-between">
                    <div class="flex items-start justify-between mb-6">
                        <div>
                            <h4 class="text-xl font-bold text-slate-900 flex items-center gap-2">
                                Pending Organizers
                                @if($pendingOrganizers > 0)
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-red-100 text-red-600 text-xs font-bold">{{ $pendingOrganizers }}</span>
                                @endif
                            </h4>
                            <p class="text-sm text-slate-500 mt-1">Review new organizer applications.</p>
                        </div>
                        <div class="p-3 bg-amber-50 rounded-xl text-amber-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                        </div>
                    </div>
                    <a href="{{ route('admin.organizers') }}" class="block w-full py-2.5 bg-[#10367d] text-white text-center font-bold rounded-xl hover:bg-[#0c2a61] transition-colors">
                        Review Now
                    </a>
                </div>

                <!-- Events Queue -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 flex flex-col justify-between">
                    <div class="flex items-start justify-between mb-6">
                        <div>
                            <h4 class="text-xl font-bold text-slate-900 flex items-center gap-2">
                                Pending Events
                                @if($pendingEvents > 0)
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-red-100 text-red-600 text-xs font-bold">{{ $pendingEvents }}</span>
                                @endif
                            </h4>
                            <p class="text-sm text-slate-500 mt-1">Review event submissions before publishing.</p>
                        </div>
                        <div class="p-3 bg-cyan-50 rounded-xl text-cyan-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>
                        </div>
                    </div>
                    <a href="{{ route('admin.events') }}" class="block w-full py-2.5 bg-[#10367d] text-white text-center font-bold rounded-xl hover:bg-[#0c2a61] transition-colors">
                        Review Now
                    </a>
                </div>
            </div>

            <!-- 4. Quick Links -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
                <h4 class="text-lg font-bold text-slate-900 mb-4">Quick Links</h4>
                <div class="flex flex-wrap gap-3">
                   <a href="{{ route('admin.transactions') }}" class="px-5 py-2 hover:bg-slate-50 border border-slate-200 rounded-full text-sm font-semibold text-slate-700 transition">Transactions</a>
                   <a href="{{ route('admin.organizers') }}" class="px-5 py-2 hover:bg-slate-50 border border-slate-200 rounded-full text-sm font-semibold text-slate-700 transition">Organizers</a>
                   <a href="{{ route('admin.events') }}" class="px-5 py-2 hover:bg-slate-50 border border-slate-200 rounded-full text-sm font-semibold text-slate-700 transition">Events</a>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
