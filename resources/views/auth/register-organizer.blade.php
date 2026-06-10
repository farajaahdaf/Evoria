<x-guest-layout>
    <div
        class="space-y-8"
        x-data="{
            step: 1,
            goToPortfolioStep() {
                if (! this.$refs.organizerForm.reportValidity()) {
                    return;
                }

                this.step = 2;
                this.$nextTick(() => document.getElementById('portfolio')?.focus());
            }
        }"
    >
        <div class="space-y-3">
            <p class="text-sm font-semibold text-primary">Daftar Event Organizer</p>
            <div class="space-y-2">
                <template x-if="step === 1">
                    <h2 class="text-2xl font-bold tracking-tight text-slate-950 sm:text-3xl">Buat akun EO dan ajukan verifikasi ke admin.</h2>
                </template>
                <template x-if="step === 2">
                    <h2 class="text-2xl font-bold tracking-tight text-slate-950 sm:text-3xl">Lengkapi portofolio EO Anda.</h2>
                </template>
                <p class="text-sm leading-6 text-slate-500">
                    <template x-if="step === 1">
                        <span>Setelah disetujui admin, akun Anda bisa dipakai untuk membuat dan mengelola event.</span>
                    </template>
                    <template x-if="step === 2">
                        <span>Dokumen ini akan membantu admin memverifikasi identitas, pengalaman, dan kelengkapan portfolio Anda.</span>
                    </template>
                </p>
            </div>

            <!-- Step Indicator -->
            <div class="flex items-center gap-3 pt-2">
                <div :class="step === 1 ? 'bg-primary w-8' : 'bg-slate-200 w-2'" class="h-1.5 rounded-full transition-all duration-300"></div>
                <div :class="step === 2 ? 'bg-primary w-8' : 'bg-slate-200 w-2'" class="h-1.5 rounded-full transition-all duration-300"></div>
            </div>
        </div>

        <form
            x-ref="organizerForm"
            method="POST"
            action="{{ route('register.organizer.store') }}"
            class="space-y-5"
            enctype="multipart/form-data"
            @submit="if (step === 1) { $event.preventDefault(); goToPortfolioStep(); }"
        >
            @csrf

            <!-- Step 1: Account Information -->
            <div x-show="step === 1" class="space-y-5" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-x-4" x-transition:enter-end="opacity-100 transform translate-x-0">
                <div class="space-y-2">
                    <label for="name" class="text-sm font-semibold text-slate-700">Nama PIC</label>
                    <input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus autocomplete="name" placeholder="Nama penanggung jawab" class="block w-full rounded-2xl border border-slate-200 bg-white px-4 py-3.5 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
                    <x-input-error :messages="$errors->get('name')" class="mt-1" />
                </div>

                <div class="space-y-2">
                    <label for="company_name" class="text-sm font-semibold text-slate-700">Nama perusahaan/komunitas</label>
                    <input id="company_name" name="company_name" type="text" value="{{ old('company_name') }}" required placeholder="Contoh: ABC Event Management" class="block w-full rounded-2xl border border-slate-200 bg-white px-4 py-3.5 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
                    <x-input-error :messages="$errors->get('company_name')" class="mt-1" />
                </div>

                <div class="space-y-2">
                    <label for="description" class="text-sm font-semibold text-slate-700">Deskripsi singkat (opsional)</label>
                    <textarea id="description" name="description" rows="3" placeholder="Ceritakan jenis event yang biasa Anda selenggarakan" class="block w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-100">{{ old('description') }}</textarea>
                    <x-input-error :messages="$errors->get('description')" class="mt-1" />
                </div>

                <div class="space-y-2">
                    <label for="email" class="text-sm font-semibold text-slate-700">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="username" placeholder="email@organizer.com" class="block w-full rounded-2xl border border-slate-200 bg-white px-4 py-3.5 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
                    <x-input-error :messages="$errors->get('email')" class="mt-1" />
                </div>

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div class="space-y-2">
                        <label for="password" class="text-sm font-semibold text-slate-700">Kata sandi</label>
                        <input id="password" name="password" type="password" required autocomplete="new-password" placeholder="Min. 8 karakter" class="block w-full rounded-2xl border border-slate-200 bg-white px-4 py-3.5 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
                    </div>
                    <div class="space-y-2">
                        <label for="password_confirmation" class="text-sm font-semibold text-slate-700">Konfirmasi sandi</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password" placeholder="Ulangi sandi" class="block w-full rounded-2xl border border-slate-200 bg-white px-4 py-3.5 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
                    </div>
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-1" />

                <button type="button" @click="goToPortfolioStep()" class="inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-primary px-5 py-4 text-sm font-extrabold text-white shadow-[0_14px_34px_rgba(37,99,235,0.28)] transition hover:-translate-y-0.5 hover:bg-primary/90">
                    Lanjutkan
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </button>
            </div>

            <!-- Step 2: Portfolio -->
            <div x-show="step === 2" class="space-y-6" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-x-4" x-transition:enter-end="opacity-100 transform translate-x-0">
                <div class="space-y-4">
                    <div class="rounded-2xl border border-blue-100 bg-blue-50/60 p-4">
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                            <div class="flex items-start gap-3">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white text-primary">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                </div>
                                <div>
                                    <h3 class="text-sm font-extrabold text-slate-900">Download template portfolio</h3>
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

                    <div class="rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50/50 p-6 transition hover:border-primary/50">
                        <div class="flex flex-col items-center text-center">
                            <div class="mb-3 flex h-12 w-12 items-center justify-center rounded-xl bg-blue-100 text-primary">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                            <label for="portfolio" class="text-sm font-bold text-slate-900">Upload portfolio yang sudah diisi</label>
                            <p class="mt-1 text-xs text-slate-500">DOCX, PDF, atau gambar. Maks. 5MB.</p>
                            <input id="portfolio" name="portfolio" type="file" accept=".docx,.pdf,image/*" class="mt-4 block w-full text-xs text-slate-500 file:mr-4 file:rounded-full file:border-0 file:bg-blue-50 file:px-4 file:py-2 file:text-xs file:font-semibold file:text-primary hover:file:bg-blue-100">
                        </div>
                    </div>
                </div>

                <div class="flex flex-col gap-3">
                    <button type="submit" class="inline-flex w-full items-center justify-center rounded-2xl bg-primary px-5 py-4 text-sm font-extrabold text-white shadow-[0_14px_34px_rgba(37,99,235,0.28)] transition hover:-translate-y-0.5 hover:bg-primary/90">
                        Daftar sebagai EO
                    </button>
                    <button type="button" @click="step = 1" class="text-sm font-bold text-slate-500 transition hover:text-slate-800">
                        Kembali ke informasi akun
                    </button>
                </div>
            </div>
        </form>

        <div class="border-t border-slate-200 pt-6 text-sm text-slate-600">
            Ingin daftar sebagai pembeli tiket?
            <a href="{{ route('register') }}" class="font-extrabold text-primary transition hover:text-primary/90 hover:underline">Daftar akun attendee</a>
        </div>
    </div>
    <style>
        [x-cloak] { display: none !important; }
    </style>
</x-guest-layout>
