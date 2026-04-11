<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
            {{ __('Attendees — ') }} {{ $event->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <div class="flex items-center justify-between">
                <h3 class="text-2xl font-bold text-gray-800">Event Attendees & Sales</h3>
                <a href="{{ route('organizer.events.show', $event->id) }}" class="text-indigo-600 hover:underline font-medium">← Back to Event</a>
            </div>

            @php
                $paidOrderItems = $orderItems->filter(fn($item) => optional($item->order)->status === 'paid');
                $totalTicketsSold = $paidOrderItems->sum('quantity');
                $totalRevenue = $paidOrderItems->sum('subtotal');
                $uniqueBuyers = $paidOrderItems->pluck('order.user_id')->filter()->unique()->count();
            @endphp

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center space-x-4">
                    <div class="p-4 bg-blue-100 rounded-xl text-blue-600">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path></svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Total Tickets Sold</p>
                        <h4 class="text-3xl font-bold text-gray-900">{{ number_format($totalTicketsSold) }}</h4>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center space-x-4">
                    <div class="p-4 bg-green-100 rounded-xl text-green-600">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Total Revenue (Paid)</p>
                        <h4 class="text-3xl font-bold text-gray-900">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h4>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center space-x-4">
                    <div class="p-4 bg-purple-100 rounded-xl text-purple-600">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Unique Buyers</p>
                        <h4 class="text-3xl font-bold text-gray-900">{{ number_format($uniqueBuyers) }}</h4>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 border-b">
                                <th class="p-4 text-sm font-semibold text-gray-600 uppercase tracking-wider">Attendee Info</th>
                                <th class="p-4 text-sm font-semibold text-gray-600 uppercase tracking-wider">Ticket Details</th>
                                <th class="p-4 text-sm font-semibold text-gray-600 uppercase tracking-wider">Subtotal</th>
                                <th class="p-4 text-sm font-semibold text-gray-600 uppercase tracking-wider">Order Status</th>
                                <th class="p-4 text-sm font-semibold text-gray-600 uppercase tracking-wider">E-Tickets</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($orderItems as $item)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="p-4">
                                    <div class="text-sm font-bold text-gray-900">{{ $item->order->user->name ?? 'Unknown User' }}</div>
                                    <div class="text-xs text-gray-500">{{ $item->order->user->email ?? 'N/A' }}</div>
                                </td>
                                <td class="p-4">
                                    <div class="text-sm font-bold text-gray-900">{{ $item->ticket->name ?? 'Deleted Ticket' }}</div>
                                    <div class="text-xs text-gray-500">Qty: {{ $item->quantity }}</div>
                                </td>
                                <td class="p-4 font-medium text-gray-900 text-sm">
                                    Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                </td>
                                <td class="p-4">
                                    @php
                                        $orderStatus = strtolower($item->order->status ?? 'pending');
                                        $statusConfig = [
                                            'paid' => ['bg' => 'bg-green-100', 'text' => 'text-green-700'],
                                            'pending' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-700'],
                                            'failed' => ['bg' => 'bg-red-100', 'text' => 'text-red-700'],
                                            'cancelled' => ['bg' => 'bg-red-100', 'text' => 'text-red-700'],
                                        ];
                                        $s = $statusConfig[$orderStatus] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-600'];
                                    @endphp
                                    <span class="px-2 py-1 rounded text-xs font-bold {{ $s['bg'] }} {{ $s['text'] }} uppercase tracking-wider">
                                        {{ $orderStatus }}
                                    </span>
                                </td>
                                <td class="p-4">
                                    @if($item->eTickets && $item->eTickets->count() > 0)
                                        <div class="space-y-1">
                                            @foreach($item->eTickets as $et)
                                                <div class="flex items-center space-x-2 text-xs">
                                                    <span class="font-mono bg-gray-100 border border-gray-200 px-2 py-0.5 rounded text-gray-700">{{ $et->ticket_code }}</span>
                                                    @php
                                                        $etStatus = strtolower($et->status);
                                                        $etColor = $etStatus === 'active' ? 'text-green-600' : ($etStatus === 'used' ? 'text-gray-500 line-through' : 'text-red-500');
                                                    @endphp
                                                    <span class="font-bold {{ $etColor }} uppercase" style="font-size: 0.65rem;">{{ $etStatus }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-xs text-gray-400 italic">No E-Ticket generated</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="p-8 text-center bg-gray-50 rounded-xl my-4">
                                    <div class="inline-block p-4 rounded-full bg-gray-100 mb-3">
                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                    </div>
                                    <p class="text-gray-500 italic font-medium">No attendees have registered for this event yet.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
