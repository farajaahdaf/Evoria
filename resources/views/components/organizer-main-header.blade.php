@php
    $user = auth()->user();
    $avatarUrl = $user && $user->profile_photo_path
        ? \Illuminate\Support\Facades\Storage::url($user->profile_photo_path)
        : "https://ui-avatars.com/api/?name=" . urlencode($user->name ?? 'Organizer') . "&background=0f172a&color=ffffff&size=128";
@endphp

<header class="bg-white border-b border-gray-200" x-data="{ organizerMenuOpen: false }">
    <div class="max-w-[1400px] mx-auto px-6 h-[80px] flex items-center justify-between gap-6">
        <div class="flex items-center gap-8">
            <a href="{{ route('home') }}" class="flex items-center gap-1 shrink-0">
                <x-application-logo class="h-10 w-auto" />
            </a>

            <nav class="hidden lg:flex items-center gap-6">
                <a class="text-[14px] font-bold text-slate-900 hover:text-primary transition-colors" href="{{ route('organizer.dashboard') }}">Dashboard Organizer</a>
                <a class="text-[14px] font-bold text-slate-900 hover:text-primary transition-colors" href="{{ route('organizer.events.index') }}">Event Saya</a>
                <a class="text-[14px] font-bold text-slate-900 hover:text-primary transition-colors" href="{{ route('organizer.events.create') }}">Buat Event</a>
            </nav>
        </div>

        <div class="flex-1 max-w-[500px] hidden md:block">
            <div class="relative">
                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-[20px]">search</span>
                <input class="w-full h-[44px] pl-11 pr-4 bg-[#F1F3F5] border-none rounded-lg text-[13px] placeholder:text-slate-400 focus:ring-1 focus:ring-primary focus:bg-white transition-colors" placeholder="Cari event, artis, atau lokasi..." type="text"/>
            </div>
        </div>

        <div class="relative flex items-center gap-3">
            <img src="{{ $avatarUrl }}" alt="Avatar {{ $user->name }}" class="w-11 h-11 rounded-full object-cover border-2 border-white shadow-sm">
            <div class="hidden sm:block">
                <p class="text-[12px] text-slate-500 leading-none">Organizer</p>
                <p class="text-[14px] font-bold text-slate-900 leading-tight">{{ $user->name }}</p>
            </div>
            <button
                type="button"
                @click="organizerMenuOpen = !organizerMenuOpen"
                class="h-9 w-9 rounded-lg border border-slate-200 bg-white flex items-center justify-center text-slate-600 hover:text-primary hover:border-primary transition-colors"
                aria-label="Buka menu organizer"
            >
                <span
                    class="material-symbols-outlined text-[20px] transition-transform duration-200"
                    :class="organizerMenuOpen ? 'rotate-180' : 'rotate-0'"
                >expand_more</span>
            </button>

            <div
                x-cloak
                x-show="organizerMenuOpen"
                @click.away="organizerMenuOpen = false"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 -translate-y-2 scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                x-transition:leave-end="opacity-0 -translate-y-1 scale-95"
                class="absolute right-0 top-14 w-52 bg-white border border-slate-200 rounded-xl shadow-lg py-2 z-50"
            >
                <a href="{{ route('organizer.events.index') }}" class="block px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 hover:text-primary transition-colors">Event Saya</a>
                <a href="{{ route('organizer.events.create') }}" class="block px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 hover:text-primary transition-colors">Buat Event</a>
                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 hover:text-primary transition-colors">Profile</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 hover:text-primary transition-colors">Logout</button>
                </form>
            </div>
        </div>
    </div>
</header>
