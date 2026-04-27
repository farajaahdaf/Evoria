<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
            {{ __('Daftar Organizer Terdaftar') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-gray-800">Organizer Terverifikasi</h3>
                    <a href="{{ route('admin.dashboard') }}" class="text-indigo-600 hover:underline">← Kembali ke Dashboard</a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 border-b">
                                <th class="p-4 text-sm font-semibold text-gray-600 uppercase tracking-wider">Nama Perusahaan</th>
                                <th class="p-4 text-sm font-semibold text-gray-600 uppercase tracking-wider">Pemilik / Email</th>
                                <th class="p-4 text-sm font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                                <th class="p-4 text-sm font-semibold text-gray-600 uppercase tracking-wider text-right">Tanggal Daftar</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($organizers as $org)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="p-4 text-gray-900 font-medium">{{ $org->company_name }}</td>
                                <td class="p-4">
                                    <div class="text-gray-900 font-medium">{{ $org->user->name }}</div>
                                    <div class="text-gray-500 text-sm">{{ $org->user->email }}</div>
                                </td>
                                <td class="p-4">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700">
                                        Verified
                                    </span>
                                </td>
                                <td class="p-4 text-right text-gray-500 text-sm">
                                    {{ $org->created_at->format('d M Y') }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="p-8 text-center text-gray-500 italic">Belum ada organizer terdaftar.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-6">
                    {{ $organizers->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>