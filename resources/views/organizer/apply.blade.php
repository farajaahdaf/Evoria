<x-app-layout>
    <div class="min-h-screen bg-[#ebebeb] py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8" x-data="{ step: 1 }">
            <div class="bg-white rounded-[32px] overflow-hidden shadow-[0_20px_80px_rgba(15,23,42,0.08)] border border-slate-200/80">
                <div class="p-8 lg:p-12">
                    <div class="mb-8 flex items-center justify-between">
                        <div>
                            <p class="text-sm font-semibold text-primary">Upgrade Akun</p>
                            <h2 class="text-3xl font-black text-slate-900 mt-1">Ajukan sebagai Organizer</h2>
                        </div>
                        <div class="flex items-center gap-3">
                            <div :class="step === 1 ? 'bg-primary text-white scale-110' : 'bg-slate-100 text-slate-400'" class="flex h-10 w-10 items-center justify-center rounded-2xl font-bold transition-all duration-300">1</div>
                            <div class="h-1 w-8 rounded-full bg-slate-100">
                                <div :class="step === 2 ? 'w-full bg-primary' : 'w-0 bg-primary'" class="h-full transition-all duration-500"></div>
                            </div>
                            <div :class="step === 2 ? 'bg-primary text-white scale-110' : 'bg-slate-100 text-slate-400'" class="flex h-10 w-10 items-center justify-center rounded-2xl font-bold transition-all duration-300">2</div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('organizer.application.store') }}" class="space-y-8" enctype="multipart/form-data" id="apply-form">
                        @csrf

                        <!-- Step 1: Info -->
                        <div x-show="step === 1" class="space-y-6" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-x-4" x-transition:enter-end="opacity-100 transform translate-x-0">
                            <div class="space-y-2">
                                <label for="company_name" class="text-sm font-semibold text-slate-700">Nama perusahaan/komunitas</label>
                                <input id="company_name" name="company_name" type="text" value="{{ old('company_name') }}" required placeholder="Contoh: ABC Event Management" class="block w-full rounded-2xl border border-slate-200 bg-white px-4 py-3.5 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-primary focus:ring-4 focus:ring-blue-100">
                                <x-input-error :messages="$errors->get('company_name')" class="mt-1" />
                            </div>

                            <div class="space-y-2">
                                <label for="description" class="text-sm font-semibold text-slate-700">Deskripsi singkat (opsional)</label>
                                <textarea id="description" name="description" rows="4" placeholder="Ceritakan jenis event yang biasa Anda selenggarakan" class="block w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-primary focus:ring-4 focus:ring-blue-100">{{ old('description') }}</textarea>
                                <x-input-error :messages="$errors->get('description')" class="mt-1" />
                            </div>

                            <div class="flex items-center justify-between pt-6 border-t border-slate-100">
                                <a href="{{ route('dashboard') }}" class="text-sm font-bold text-slate-400 transition hover:text-slate-600">Batal</a>
                                <button type="button" @click="step = 2" class="inline-flex items-center gap-2 rounded-2xl bg-primary px-8 py-4 text-sm font-extrabold text-white shadow-lg transition hover:-translate-y-0.5 hover:bg-primary/90">
                                    Langkah Selanjutnya
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                </button>
                            </div>
                        </div>

                        <!-- Step 2: Portfolio -->
                        <div x-show="step === 2" class="space-y-8" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-x-4" x-transition:enter-end="opacity-100 transform translate-x-0">
                            <div class="mx-auto max-w-xl space-y-4">
                                <div class="rounded-3xl border border-blue-100 bg-blue-50/60 p-5">
                                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                                        <div class="flex items-start gap-3 text-left">
                                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-white text-primary">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                            </div>
                                            <div>
                                                <h3 class="text-sm font-black text-slate-900">Download template portfolio</h3>
                                                <p class="mt-1 text-xs leading-5 text-slate-500">Isi template DOCX dari Evoria terlebih dahulu sebelum mengunggah portfolio.</p>
                                            </div>
                                        </div>
                                        <a
                                            href="{{ asset('templates/template_portofolio_eo_evoria.docx') }}"
                                            download
                                            class="inline-flex items-center justify-center gap-2 rounded-full bg-primary px-4 py-2.5 text-xs font-extrabold text-white shadow-sm transition hover:bg-primary/90"
                                        >
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v10m0 0l-4-4m4 4l4-4M4 20h16"></path></svg>
                                            Download DOCX
                                        </a>
                                    </div>
                                </div>

                                <div class="rounded-3xl border-2 border-dashed border-slate-200 bg-slate-50/50 p-8 text-center transition hover:border-primary/50">
                                    <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-blue-100 text-primary">
                                        <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    </div>
                                    <label for="portfolio" class="block text-base font-bold text-slate-900">Upload portfolio yang sudah diisi</label>
                                    <p class="mt-1 text-xs text-slate-500">DOCX, PDF, atau gambar. Maks. 5MB.</p>
                                    <input id="portfolio" name="portfolio" type="file" accept=".docx,.pdf,image/*" class="mt-6 block w-full text-xs text-slate-500 file:mr-4 file:rounded-full file:border-0 file:bg-primary file:px-4 file:py-2 file:text-xs file:font-bold file:text-white hover:file:bg-primary/90">
                                </div>
                            </div>

                            <div class="flex items-center justify-between pt-6 border-t border-slate-100">
                                <button type="button" @click="step = 1" class="text-sm font-bold text-slate-400 transition hover:text-slate-600 flex items-center gap-2">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                                    Kembali ke Detail
                                </button>
                                <button type="submit" class="inline-flex items-center gap-2 rounded-2xl bg-primary px-10 py-4 text-sm font-extrabold text-white shadow-lg transition hover:-translate-y-0.5 hover:bg-primary/90">
                                    Kirim Pengajuan
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <style>
        [x-cloak] { display: none !important; }
    </style>
</x-app-layout>
