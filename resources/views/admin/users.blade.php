<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
            {{ __('Daftar Attendee Terdaftar') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-gray-800">Semua Attendee</h3>
                    <a href="{{ route('admin.dashboard') }}" class="text-indigo-600 hover:underline">← Kembali ke Dashboard</a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 border-b">
                                <th class="p-4 text-sm font-semibold text-gray-600 uppercase tracking-wider">Nama</th>
                                <th class="p-4 text-sm font-semibold text-gray-600 uppercase tracking-wider">Email</th>
                                <th class="p-4 text-sm font-semibold text-gray-600 uppercase tracking-wider">Peran</th>
                                <th class="p-4 text-sm font-semibold text-gray-600 uppercase tracking-wider text-right">Tanggal Daftar</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($users as $user)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="p-4 text-gray-900 font-medium">{{ $user->name }}</td>
                                <td class="p-4 text-gray-600">{{ $user->email }}</td>
                                <td class="p-4">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                        {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-700' : ($user->role === 'organizer' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700') }}">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </td>
                                <td class="p-4 text-right text-gray-500 text-sm">
                                    {{ $user->created_at->format('d M Y') }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="p-8 text-center text-gray-500 italic">Belum ada pengguna terdaftar.</td>
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