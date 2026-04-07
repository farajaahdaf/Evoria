<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
            {{ __('Organizer Verification') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100 space-y-5">
                @if(session('success'))
                    <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-700">
                        {{ session('error') }}
                    </div>
                @endif

                <div>
                    <p class="text-sm font-semibold text-indigo-600">Status pendaftaran EO</p>
                    @if (($profile?->status ?? 'pending') === 'rejected')
                        <h3 class="text-2xl font-bold text-gray-900 mt-1">Pengajuan Anda belum disetujui admin.</h3>
                        <p class="text-gray-600 mt-2">
                            Silakan perbarui profil Anda jika diperlukan lalu hubungi admin untuk pengajuan ulang.
                        </p>
                    @else
                        <h3 class="text-2xl font-bold text-gray-900 mt-1">Akun Anda sedang menunggu persetujuan admin.</h3>
                        <p class="text-gray-600 mt-2">
                            Setelah diverifikasi, Anda bisa mengakses dashboard organizer dan membuat event.
                        </p>
                    @endif
                </div>

                <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 text-sm">
                    <p class="text-gray-500">Perusahaan</p>
                    <p class="font-semibold text-gray-800">{{ $profile?->company_name ?? '-' }}</p>
                    <p class="text-gray-500 mt-3">Status</p>
                    <p class="font-semibold uppercase {{ ($profile?->status ?? 'pending') === 'verified' ? 'text-green-600' : 'text-amber-600' }}">
                        {{ $profile?->status ?? 'pending' }}
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('profile.edit') }}" class="px-5 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                        Lihat Profil
                    </a>
                    <a href="{{ route('logout') }}"
                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                       class="px-5 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                        Logout
                    </a>
                    <form id="logout-form" method="POST" action="{{ route('logout') }}" class="hidden">
                        @csrf
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
