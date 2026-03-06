<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
            {{ __('Admin Control Panel') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <div class="bg-gradient-to-r from-gray-800 to-gray-900 rounded-2xl p-8 shadow-xl text-white flex items-center justify-between">
                <div>
                    <h3 class="text-3xl font-bold mb-2">System Overview 🛡️</h3>
                    <p class="text-gray-300 text-lg">Monitor the marketplace, verify organizers, and approve events.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Organizers Queue -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="p-4 bg-orange-100 rounded-xl text-orange-600">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 font-medium">Pending Organizers</p>
                            <h4 class="text-3xl font-bold text-gray-900">{{ $pendingOrganizers ?? 0 }}</h4>
                        </div>
                    </div>
                    <a href="{{ route('admin.organizers') }}" class="px-5 py-2 bg-orange-50 text-orange-600 font-semibold rounded-lg hover:bg-orange-100 transition">Review</a>
                </div>

                <!-- Events Queue -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="p-4 bg-blue-100 rounded-xl text-blue-600">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 font-medium">Pending Events</p>
                            <h4 class="text-3xl font-bold text-gray-900">{{ $pendingEvents ?? 0 }}</h4>
                        </div>
                    </div>
                    <a href="{{ route('admin.events') }}" class="px-5 py-2 bg-blue-50 text-blue-600 font-semibold rounded-lg hover:bg-blue-100 transition">Review</a>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <h4 class="text-xl font-bold text-gray-800 mb-4">Quick Links</h4>
                <div class="flex space-x-4">
                   <a href="#" class="px-4 py-2 border rounded-full text-sm font-medium hover:bg-gray-50 text-gray-700">All Transactions</a>
                   <a href="#" class="px-4 py-2 border rounded-full text-sm font-medium hover:bg-gray-50 text-gray-700">User Management</a>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
