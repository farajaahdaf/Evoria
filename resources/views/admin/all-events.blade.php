<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
            {{ __('Daftar Seluruh Event') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-gray-800">Semua Event</h3>
                    <a href="{{ route('admin.dashboard') }}" class="text-indigo-600 hover:underline">← Kembali ke Dashboard</a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 border-b">
                                <th class="p-4 text-sm font-semibold text-gray-600 uppercase tracking-wider">Info Event</th>
                                <th class="p-4 text-sm font-semibold text-gray-600 uppercase tracking-wider">Organizer</th>
                                <th class="p-4 text-sm font-semibold text-gray-600 uppercase tracking-wider text-right">Status</th>
                                <th class="p-4 text-sm font-semibold text-gray-600 uppercase tracking-wider text-right">Jadwal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($events as $event)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="p-4">
                                    <div class="text-gray-900 font-bold border-b border-dashed pb-1 mb-1">{{ $event->title }}</div>
                                    <div class="text-sm text-gray-600 mt-1"><span class="font-semibold">Lok:</span> {{ $event->location_name }}</div>
                                </td>
                                <td class="p-4 text-gray-700">
                                    {{ $event->organizer->name }}
                                </td>
                                <td class="p-4 text-right">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                        {{ $event->status === 'published' ? 'bg-green-100 text-green-700' :
                                           ($event->status === 'pending_review' ? 'bg-orange-100 text-orange-700' :
                                           ($event->status === 'rejected' ? 'bg-red-100 text-red-700' :
                                           'bg-gray-100 text-gray-700')) }}">
                                        {{ ucfirst(str_replace('_', ' ', $event->status)) }}
                                    </span>
                                </td>
                                <td class="p-4 text-right text-gray-600 text-sm">
                                    <div>{{ $event->start_time->format('d M Y, H:i') }}</div>
                                    <a href="{{ route('admin.events.show', $event->slug) }}" class="mt-2 inline-flex text-xs font-bold text-indigo-600 hover:text-indigo-800">Lihat detail</a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="p-8 text-center text-gray-500 italic">Belum ada event terdaftar.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-6">
                    {{ $events->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
