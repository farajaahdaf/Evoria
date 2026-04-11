<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
            {{ __('Transaction Monitoring') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-gray-800">All Transactions</h3>
                    <a href="{{ route('admin.dashboard') }}" class="text-indigo-600 hover:underline">← Back to Dashboard</a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 border-b">
                                <th class="p-4 text-sm font-semibold text-gray-600 uppercase tracking-wider">Order No. / Date</th>
                                <th class="p-4 text-sm font-semibold text-gray-600 uppercase tracking-wider">Buyer</th>
                                <th class="p-4 text-sm font-semibold text-gray-600 uppercase tracking-wider">Event</th>
                                <th class="p-4 text-sm font-semibold text-gray-600 uppercase tracking-wider">Payment Method</th>
                                <th class="p-4 text-sm font-semibold text-gray-600 uppercase tracking-wider text-right">Total</th>
                                <th class="p-4 text-sm font-semibold text-gray-600 uppercase tracking-wider text-right">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($orders as $order)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="p-4">
                                    <div class="font-bold text-gray-900 border-b border-dashed pb-1 mb-1">{{ $order->order_number }}</div>
                                    <div class="text-xs text-gray-500">{{ $order->created_at->format('M d, Y H:i') }}</div>
                                </td>
                                <td class="p-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $order->user->name ?? 'N/A' }}</div>
                                    <div class="text-xs text-gray-500">{{ $order->user->email ?? 'N/A' }}</div>
                                </td>
                                <td class="p-4">
                                    <div class="text-sm text-gray-700">
                                        @foreach($order->orderItems as $item)
                                            <div class="mb-1">
                                                <span class="font-medium">{{ $item->ticket->event->title ?? 'Deleted Event' }}</span>
                                                <br>
                                                <span class="text-xs text-gray-500">{{ $item->quantity }}x {{ $item->ticket->name ?? 'Ticket' }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="p-4">
                                    <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded text-xs font-medium uppercase tracking-wider">
                                        {{ $order->payment_method ?: 'N/A' }}
                                    </span>
                                </td>
                                <td class="p-4 text-right font-medium text-gray-900">
                                    Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                                </td>
                                <td class="p-4 text-right">
                                    @php
                                        $statusConfig = [
                                            'paid' => ['bg' => 'bg-green-100', 'text' => 'text-green-700'],
                                            'pending' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-700'],
                                            'failed' => ['bg' => 'bg-red-100', 'text' => 'text-red-700'],
                                            'cancelled' => ['bg' => 'bg-red-100', 'text' => 'text-red-700'],
                                            'refunded' => ['bg' => 'bg-gray-200', 'text' => 'text-gray-700'],
                                        ];
                                        $s = $statusConfig[strtolower($order->status)] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-600'];
                                    @endphp
                                    <span class="px-3 py-1 rounded-full text-xs font-bold {{ $s['bg'] }} {{ $s['text'] }} uppercase tracking-wider">
                                        {{ $order->status }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="p-8 text-center bg-gray-50 rounded-xl my-4">
                                    <div class="inline-block p-4 rounded-full bg-gray-100 mb-3">
                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                    </div>
                                    <p class="text-gray-500 italic font-medium">No transactions have been recorded yet.</p>
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
