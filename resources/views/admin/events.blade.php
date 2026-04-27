<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
            {{ __('Pending Events') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-gray-800">Event Approval Queue</h3>
                    <a href="{{ route('admin.dashboard') }}" class="text-indigo-600 hover:underline">← Back to Dashboard</a>
                </div>

                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                      <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                @endif

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 border-b">
                                <th class="p-4 text-sm font-semibold text-gray-600 uppercase tracking-wider">Event Info</th>
                                <th class="p-4 text-sm font-semibold text-gray-600 uppercase tracking-wider">Organizer</th>
                                <th class="p-4 text-sm font-semibold text-gray-600 uppercase tracking-wider">Schedule</th>
                                <th class="p-4 text-sm font-semibold text-gray-600 uppercase tracking-wider text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($events as $event)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="p-4">
                                    <div class="text-gray-900 font-bold border-b border-dashed pb-1 mb-1">{{ $event->title }}</div>
                                    <div class="text-sm text-gray-600 mt-1"><span class="font-semibold">Loc:</span> {{ $event->location_name }}</div>
                                </td>
                                <td class="p-4 text-gray-700">
                                    {{ $event->organizer->name }}
                                </td>
                                <td class="p-4 text-gray-600 text-sm">
                                    {{ $event->start_time->format('M d, Y H:i') }} <br/>to<br/> {{ $event->end_time->format('M d, Y H:i') }}
                                </td>
                                <td class="p-4 text-right">
                                    <form action="{{ route('admin.events.approve', $event->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium shadow-sm transition">Approve</button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="p-8 text-center text-gray-500 italic">No events pending approval. Queue is clear!</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>
