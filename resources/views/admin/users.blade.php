<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
            {{ __('Registered Attendees') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <div class="flex flex-col gap-4">
                    <div>
                        <p class="text-xs font-black text-blue-600 uppercase tracking-[0.22em]">Attendees</p>
                        <h3 class="mt-2 text-2xl font-black text-gray-900">Registered Attendees</h3>
                        <p class="mt-2 text-sm font-medium text-gray-500">
                            Review attendee accounts registered on Evoria.
                        </p>
                    </div>
                </div>

                @php
                    $sortOptions = [
                        'newest' => 'Newest Registration',
                        'oldest' => 'Oldest Registration',
                    ];
                @endphp

                <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between border-t border-gray-100 pt-6">
                    <p class="text-sm font-bold text-gray-500">{{ $users->total() }} attendee records</p>
                    <form method="GET" action="{{ route('admin.users') }}" class="flex items-center gap-2">
                        <select id="attendee-sort" name="sort" onchange="this.form.submit()" class="rounded-xl border-gray-200 text-sm font-bold text-gray-700 focus:border-blue-500 focus:ring-blue-500">
                            @foreach($sortOptions as $value => $label)
                                <option value="{{ $value }}" @selected($sort === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </form>
                </div>

                <div class="mt-6 overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 border-b">
                                <th class="p-4 text-sm font-semibold text-gray-600 uppercase tracking-wider">Name</th>
                                <th class="p-4 text-sm font-semibold text-gray-600 uppercase tracking-wider">Email</th>
                                <th class="p-4 text-sm font-semibold text-gray-600 uppercase tracking-wider text-right">Orders</th>
                                <th class="p-4 text-sm font-semibold text-gray-600 uppercase tracking-wider text-right">Paid Tickets</th>
                                <th class="p-4 text-sm font-semibold text-gray-600 uppercase tracking-wider text-right">Total Spent</th>
                                <th class="p-4 text-sm font-semibold text-gray-600 uppercase tracking-wider text-right">Registration Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($users as $user)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="p-4 text-gray-900 font-medium">{{ $user->name }}</td>
                                    <td class="p-4 text-gray-600">{{ $user->email }}</td>
                                    <td class="p-4 text-right font-bold text-gray-900">
                                        {{ number_format($user->orders_count) }}
                                    </td>
                                    <td class="p-4 text-right font-bold text-gray-900">
                                        {{ number_format((int) $user->paid_tickets_count) }}
                                    </td>
                                    <td class="p-4 text-right font-bold text-gray-900">
                                        Rp {{ number_format((float) ($user->total_spent ?? 0), 0, ',', '.') }}
                                    </td>
                                    <td class="p-4 text-right text-gray-500 text-sm">
                                        {{ $user->created_at->format('d M Y') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-8 text-center text-gray-500 italic">No registered users yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-6">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
