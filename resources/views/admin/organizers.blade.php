<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
            {{ __('Pending Organizers') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-gray-800">Verification Queue</h3>
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
                                <th class="p-4 text-sm font-semibold text-gray-600 uppercase tracking-wider">Company Name</th>
                                <th class="p-4 text-sm font-semibold text-gray-600 uppercase tracking-wider">Owner / Email</th>
                                <th class="p-4 text-sm font-semibold text-gray-600 uppercase tracking-wider">Description</th>
                                <th class="p-4 text-sm font-semibold text-gray-600 uppercase tracking-wider text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($organizers as $org)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="p-4 text-gray-900 font-medium">{{ $org->company_name }}</td>
                                <td class="p-4">
                                    <div class="text-gray-900">{{ $org->user->name }}</div>
                                    <div class="text-gray-500 text-sm">{{ $org->user->email }}</div>
                                </td>
                                <td class="p-4 text-gray-600 text-sm max-w-xs truncate">{{ $org->description }}</td>
                                <td class="p-4 text-right">
                                    <form action="{{ route('admin.organizers.verify', $org->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-medium shadow-sm transition">Verify</button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="p-8 text-center text-gray-500 italic">No organizers pending verification. Great job!</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>
