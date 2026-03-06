<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
            {{ __('Organizer Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Welcome Banner -->
            <div class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-2xl p-8 shadow-xl text-white flex items-center justify-between">
                <div>
                    <h3 class="text-3xl font-bold mb-2">Welcome back, {{ auth()->user()->name }}! 🚀</h3>
                    <p class="text-blue-100 text-lg">Manage your events, view your sales, and grow your audience all in one place.</p>
                </div>
                <div class="hidden md:block">
                    <a href="{{ route('organizer.events.create') }}" class="px-6 py-3 bg-white text-indigo-600 font-bold rounded-full shadow-lg hover:bg-gray-50 transition transform hover:-translate-y-1">
                        + Create New Event
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Stat Card 1 -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center space-x-4">
                    <div class="p-3 bg-indigo-100 rounded-xl text-indigo-600">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Total Events</p>
                        <h4 class="text-2xl font-bold text-gray-900">{{ $eventsCount ?? 0 }}</h4>
                    </div>
                </div>

                <!-- Stat Card 2 -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center space-x-4">
                    <div class="p-3 bg-green-100 rounded-xl text-green-600">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Profile Status</p>
                        @php $status = auth()->user()->organizerProfile?->status ?? 'Missing'; @endphp
                        <h4 class="text-2xl font-bold uppercase {{ $status == 'verified' ? 'text-green-600' : 'text-yellow-600' }}">
                            {{ $status }}
                        </h4>
                    </div>
                </div>

                <!-- Stat Card 3 -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center space-x-4">
                    <div class="p-3 bg-pink-100 rounded-xl text-pink-600">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path></svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Total Tickets Sold</p>
                        <h4 class="text-2xl font-bold text-gray-900">0</h4>
                    </div>
                </div>
            </div>

            <!-- Manage section -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <div class="flex items-center justify-between mb-6">
                    <h4 class="text-xl font-bold text-gray-800">Quick Actions</h4>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <a href="{{ route('organizer.events.index') }}" class="block p-6 border rounded-xl hover:border-indigo-500 hover:shadow-md transition">
                        <h5 class="text-lg font-bold text-indigo-600">My Events</h5>
                        <p class="text-gray-500 mt-1">View and manage all events you've hosted.</p>
                    </a>
                    <a href="#" class="block p-6 border rounded-xl hover:border-indigo-500 hover:shadow-md transition">
                        <h5 class="text-lg font-bold text-indigo-600">Scan QR Code</h5>
                        <p class="text-gray-500 mt-1">Open scanner to check-in attendees.</p>
                    </a>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
