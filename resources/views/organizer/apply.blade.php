<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Apply as Event Organizer') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100 space-y-6">
                <div>
                    <p class="text-sm font-semibold text-indigo-600">Upgrade akun attendee</p>
                    <h3 class="text-2xl font-bold text-gray-900 mt-1">Ajukan akun Anda sebagai Event Organizer</h3>
                    <p class="text-gray-600 mt-2">
                        Setelah pengajuan dikirim, akun Anda akan ditinjau admin. Jika disetujui, Anda dapat membuat dan mengelola event.
                    </p>
                </div>

                <form method="POST" action="{{ route('organizer.application.store') }}" class="space-y-5">
                    @csrf

                    <div class="space-y-2">
                        <label for="company_name" class="text-sm font-semibold text-slate-700">Nama perusahaan/komunitas</label>
                        <input id="company_name" name="company_name" type="text" value="{{ old('company_name') }}" required placeholder="Contoh: ABC Event Management" class="block w-full rounded-2xl border border-slate-200 bg-white px-4 py-3.5 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
                        <x-input-error :messages="$errors->get('company_name')" class="mt-1" />
                    </div>

                    <div class="space-y-2">
                        <label for="description" class="text-sm font-semibold text-slate-700">Deskripsi singkat (opsional)</label>
                        <textarea id="description" name="description" rows="4" placeholder="Ceritakan jenis event yang biasa Anda selenggarakan" class="block w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-100">{{ old('description') }}</textarea>
                        <x-input-error :messages="$errors->get('description')" class="mt-1" />
                    </div>

                    <div class="flex items-center gap-3">
                        <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-5 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-indigo-700">
                            Kirim Pengajuan EO
                        </button>
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center rounded-xl border border-gray-300 px-5 py-3 text-sm font-semibold text-gray-700 transition hover:bg-gray-50">
                            Kembali
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
