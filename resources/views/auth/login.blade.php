<x-guest-layout>
    <div class="space-y-8">
        <div class="space-y-3">
            <p class="text-sm font-semibold text-blue-700">Masuk ke Evoria</p>
            <div class="space-y-2">
                <h2 class="text-2xl font-bold tracking-tight text-slate-950 sm:text-3xl">Akses dashboard, tiket, dan event unggulan Anda.</h2>
                <p class="text-sm leading-6 text-slate-500">
                    Gunakan akun Anda untuk melanjutkan pengalaman yang sama seperti di beranda dan dashboard.
                </p>
            </div>
        </div>

        @if (session('status'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
                {{ session('status') }}
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST" class="space-y-5">
            @csrf

            <div class="space-y-2">
                <label for="email" class="text-sm font-semibold text-slate-700">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="nama@email.com" class="block w-full rounded-2xl border border-slate-200 bg-white px-4 py-3.5 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
                @error('email')
                    <p class="text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="space-y-2" x-data="{ show: false }">
                <div class="flex items-center justify-between">
                    <label for="password" class="text-sm font-semibold text-slate-700">Kata sandi</label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-xs font-bold text-blue-600 transition hover:text-blue-700 hover:underline">Lupa kata sandi?</a>
                    @endif
                </div>

                <div class="relative">
                    <input id="password" name="password" :type="show ? 'text' : 'password'" required autocomplete="current-password" placeholder="Masukkan kata sandi" class="block w-full rounded-2xl border border-slate-200 bg-white px-4 py-3.5 pr-12 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
                    <button type="button" @click="show = !show" class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 transition hover:text-slate-600">
                        <svg x-show="!show" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <svg x-cloak x-show="show" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                        </svg>
                    </button>
                </div>

                @error('password')
                    <p class="text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <label for="remember" class="flex items-center gap-3 rounded-2xl border border-slate-200/80 bg-slate-50/80 px-4 py-3 text-sm text-slate-600">
                <input id="remember" name="remember" type="checkbox" class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                Tetap masuk di perangkat ini
            </label>

            <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-primary px-5 py-4 text-sm font-extrabold text-white shadow-[0_14px_34px_rgba(37,99,235,0.28)] transition hover:-translate-y-0.5 hover:bg-primary/90">
                Masuk sekarang
                <svg class="h-4 w-4 transition group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                </svg>
            </button>
        </form>

        <div class="border-t border-slate-200 pt-6 text-sm text-slate-600">
            Belum punya akun?
            <a href="{{ route('register') }}" class="font-extrabold text-primary transition hover:text-primary/90 hover:underline">Daftar akun Evoria</a>
        </div>

        <div class="rounded-2xl border border-indigo-100 bg-indigo-50/70 px-4 py-3 text-sm text-indigo-800">
            Ingin jadi penyelenggara event?
            <a href="{{ route('register.organizer') }}" class="font-bold text-primary underline underline-offset-2">Ajukan akun Event Organizer</a>
        </div>
    </div>
</x-guest-layout>
