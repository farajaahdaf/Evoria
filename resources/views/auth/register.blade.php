<x-guest-layout>
    <div class="space-y-8">
        <div class="space-y-3">
            <p class="text-sm font-semibold text-blue-700">Buat akun Evoria</p>
            <div class="space-y-2">
                <h2 class="text-2xl font-bold tracking-tight text-slate-950 sm:text-3xl">Mulai dari satu akun untuk beli tiket dan mengelola event.</h2>
                <p class="text-sm leading-6 text-slate-500">
                    Registrasi cepat untuk masuk ke pengalaman event marketplace yang sama dengan dashboard utama.
                </p>
            </div>
        </div>

        <form method="POST" action="{{ route('register') }}" class="space-y-5">
            @csrf

            <div class="space-y-2">
                <label for="name" class="text-sm font-semibold text-slate-700">Nama lengkap</label>
                <input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus autocomplete="name" placeholder="Nama Anda" class="block w-full rounded-2xl border border-slate-200 bg-white px-4 py-3.5 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
                <x-input-error :messages="$errors->get('name')" class="mt-1" />
            </div>

            <div class="space-y-2">
                <label for="email" class="text-sm font-semibold text-slate-700">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="username" placeholder="nama@email.com" class="block w-full rounded-2xl border border-slate-200 bg-white px-4 py-3.5 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
                <x-input-error :messages="$errors->get('email')" class="mt-1" />
            </div>

            <div class="space-y-2">
                <label for="password" class="text-sm font-semibold text-slate-700">Kata sandi</label>
                <input id="password" name="password" type="password" required autocomplete="new-password" placeholder="Minimal 8 karakter" class="block w-full rounded-2xl border border-slate-200 bg-white px-4 py-3.5 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
                <x-input-error :messages="$errors->get('password')" class="mt-1" />
            </div>

            <div class="space-y-2">
                <label for="password_confirmation" class="text-sm font-semibold text-slate-700">Konfirmasi kata sandi</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password" placeholder="Ulangi kata sandi" class="block w-full rounded-2xl border border-slate-200 bg-white px-4 py-3.5 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
            </div>

            <button type="submit" class="inline-flex w-full items-center justify-center rounded-2xl bg-blue-600 px-5 py-4 text-sm font-extrabold text-white shadow-[0_14px_34px_rgba(37,99,235,0.28)] transition hover:-translate-y-0.5 hover:bg-blue-700">
                Buat akun
            </button>
        </form>

        <div class="border-t border-slate-200 pt-6 text-sm text-slate-600">
            Sudah punya akun?
            <a href="{{ route('login') }}" class="font-extrabold text-blue-600 transition hover:text-blue-700 hover:underline">Masuk di sini</a>
        </div>

        <div class="rounded-2xl border border-indigo-100 bg-indigo-50/70 px-4 py-3 text-sm text-indigo-800">
            Ingin membuat event sebagai penyelenggara?
            <a href="{{ route('register.organizer') }}" class="font-bold text-indigo-700 underline underline-offset-2">Daftar sebagai Event Organizer</a>
        </div>
    </div>
</x-guest-layout>
