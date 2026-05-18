<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
            {{ __('Transaction Monitoring') }}
        </h2>
    </x-slot>

    @php
        $tabs = [
            'all' => 'All',
            'pending' => 'Pending',
            'paid' => 'Paid',
            'issues' => 'Issues',
        ];

        $statusConfig = [
            'paid' => ['class' => 'bg-green-100 text-green-700 border-green-200', 'label' => 'Paid'],
            'pending' => ['class' => 'bg-yellow-100 text-yellow-700 border-yellow-200', 'label' => 'Pending'],
            'failed' => ['class' => 'bg-red-100 text-red-700 border-red-200', 'label' => 'Failed'],
            'cancelled' => ['class' => 'bg-red-100 text-red-700 border-red-200', 'label' => 'Cancelled'],
            'refunded' => ['class' => 'bg-gray-100 text-gray-700 border-gray-200', 'label' => 'Refunded'],
        ];

        $sortOptions = [
            'newest' => 'Newest Transaction',
            'oldest' => 'Oldest Transaction',
            'amount_high' => 'Highest Amount',
            'amount_low' => 'Lowest Amount',
        ];
    @endphp

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <div class="flex flex-col gap-4">
                    <div>
                        <p class="text-xs font-black text-purple-600 uppercase tracking-[0.22em]">Transactions</p>
                        <h3 class="mt-2 text-2xl font-black text-gray-900">Transaction Monitoring</h3>
                        <p class="mt-2 text-sm font-medium text-gray-500">
                            Monitor orders by operational status. Issues include failed and cancelled transactions.
                        </p>
                    </div>
                </div>

                <div class="mt-6 flex gap-2 overflow-x-auto border-b border-gray-100 pb-3">
                    @foreach($tabs as $tabStatus => $label)
                        @php $isActive = $status === $tabStatus; @endphp
                        <a
                            href="{{ route('admin.transactions', ['status' => $tabStatus, 'sort' => $sort]) }}"
                            class="shrink-0 inline-flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-extrabold transition-colors {{ $isActive ? 'bg-gray-900 text-white shadow-sm' : 'bg-gray-50 text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}"
                        >
                            <span>{{ $label }}</span>
                            <span class="rounded-full px-2 py-0.5 text-[11px] font-black {{ $isActive ? 'bg-white/15 text-white' : 'bg-white text-gray-500 border border-gray-100' }}">
                                {{ $statusCounts[$tabStatus] ?? 0 }}
                            </span>
                        </a>
                    @endforeach
                </div>

                <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-sm font-bold text-gray-500">{{ $statusCounts[$status] ?? 0 }} transaction records</p>
                    <form method="GET" action="{{ route('admin.transactions') }}" class="flex items-center gap-2">
                        <input type="hidden" name="status" value="{{ $status }}">
                        <select id="transaction-sort" name="sort" onchange="this.form.submit()" class="rounded-xl border-gray-200 text-sm font-bold text-gray-700 focus:border-blue-500 focus:ring-blue-500">
                            @foreach($sortOptions as $value => $label)
                                <option value="{{ $value }}" @selected($sort === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </form>
                </div>

                <div class="mt-6 overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-100">
                                <th class="p-4 text-xs font-black text-gray-500 uppercase tracking-wider">Order No. / Date</th>
                                <th class="p-4 text-xs font-black text-gray-500 uppercase tracking-wider">Buyer</th>
                                <th class="p-4 text-xs font-black text-gray-500 uppercase tracking-wider">Event</th>
                                <th class="p-4 text-xs font-black text-gray-500 uppercase tracking-wider">Payment Method</th>
                                <th class="p-4 text-xs font-black text-gray-500 uppercase tracking-wider text-right">Total</th>
                                <th class="p-4 text-xs font-black text-gray-500 uppercase tracking-wider text-right">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($orders as $order)
                                @php
                                    $orderStatus = strtolower($order->status);
                                    $statusMeta = $statusConfig[$orderStatus] ?? ['class' => 'bg-gray-100 text-gray-700 border-gray-200', 'label' => ucfirst($orderStatus)];
                                @endphp
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="p-4 align-top">
                                        <div class="font-bold text-gray-900 border-b border-dashed pb-1 mb-1">{{ $order->order_number }}</div>
                                        <div class="text-xs text-gray-500">{{ $order->created_at->format('M d, Y H:i') }}</div>
                                    </td>
                                    <td class="p-4 align-top">
                                        <div class="text-sm font-bold text-gray-900">{{ $order->user->name ?? 'N/A' }}</div>
                                        <div class="text-xs text-gray-500">{{ $order->user->email ?? 'N/A' }}</div>
                                    </td>
                                    <td class="p-4 align-top">
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
                                    <td class="p-4 align-top">
                                        <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded text-xs font-medium uppercase tracking-wider">
                                            {{ $order->payment_method ?: 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="p-4 align-top text-right font-medium text-gray-900">
                                        Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                                    </td>
                                    <td class="p-4 align-top text-right">
                                        <span class="inline-flex rounded-full border px-3 py-1 text-xs font-black uppercase tracking-wider {{ $statusMeta['class'] }}">
                                            {{ $statusMeta['label'] }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-10 text-center text-sm font-bold italic text-gray-400">
                                        No transactions found in this tab.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-6">
                    {{ $orders->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
