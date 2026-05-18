@php
    $user = auth()->user();
    $avatarUrl = $user && $user->profile_photo_path
        ? \Illuminate\Support\Facades\Storage::url($user->profile_photo_path)
        : "https://ui-avatars.com/api/?name=" . urlencode($user->name ?? 'User') . "&background=0f172a&color=ffffff&size=128";
    $role = $user->role ?? 'user';
    $roleLabel = match($role) {
        'admin' => 'Admin',
        'organizer' => 'Organizer',
        default => 'User',
    };
    $logoRoute = $role === 'admin' ? route('admin.dashboard') : route('home');
@endphp

<header class="bg-white border-b border-gray-200" x-data="{ menuOpen: false }">
    <div class="max-w-[1400px] mx-auto px-6 h-[80px] flex items-center justify-between gap-6">
        <div class="flex items-center gap-8">
            <a href="{{ $logoRoute }}" class="flex items-center gap-1 shrink-0">
                <x-application-logo class="h-10 w-auto" />
            </a>

            <nav class="hidden lg:flex items-center gap-6">
                @if($role === 'admin')
                    <a class="text-[14px] font-bold hover:text-primary transition-colors {{ request()->routeIs('admin.users') ? 'text-primary' : 'text-slate-900' }}" href="{{ route('admin.users') }}">Attendee</a>
                    <a class="text-[14px] font-bold hover:text-primary transition-colors {{ request()->routeIs('admin.organizers') || request()->routeIs('admin.organizers.*') ? 'text-primary' : 'text-slate-900' }}" href="{{ route('admin.organizers.all') }}">Organizer</a>
                    <a class="text-[14px] font-bold hover:text-primary transition-colors {{ request()->routeIs('admin.events') || request()->routeIs('admin.events.*') ? 'text-primary' : 'text-slate-900' }}" href="{{ route('admin.events.all') }}">Events</a>
                    <a class="text-[14px] font-bold hover:text-primary transition-colors {{ request()->routeIs('admin.transactions') ? 'text-primary' : 'text-slate-900' }}" href="{{ route('admin.transactions') }}">Transactions</a>
                @elseif($role === 'organizer')
                    <a class="text-[14px] font-bold hover:text-primary transition-colors {{ request()->routeIs('organizer.balance') ? 'text-primary' : 'text-slate-900' }}" href="{{ route('organizer.balance') }}">Saldo</a>
                    <a class="text-[14px] font-bold hover:text-primary transition-colors {{ request()->routeIs('organizer.events.index') ? 'text-primary' : 'text-slate-900' }}" href="{{ route('organizer.events.index') }}">Event Saya</a>
                    <a class="text-[14px] font-bold hover:text-primary transition-colors {{ request()->routeIs('organizer.events.create') ? 'text-primary' : 'text-slate-900' }}" href="{{ route('organizer.events.create') }}">Buat Event</a>
                @endif
            </nav>
        </div>

        @if($role !== 'admin')
            <div class="flex-1 max-w-[500px] hidden md:block">
                <x-event-search :initial-value="request('q', '')" />
            </div>
        @else
            <div class="flex-1 hidden md:block"></div>
        @endif

        <div class="relative flex items-center gap-3">
            <img src="{{ $avatarUrl }}" alt="Avatar {{ $user->name }}" class="w-11 h-11 rounded-full object-cover border-2 border-white shadow-sm">
            <div class="hidden sm:block">
                <p class="text-[12px] text-slate-500 leading-none">{{ $roleLabel }}</p>
                <p class="text-[14px] font-bold text-slate-900 leading-tight">{{ $user->name }}</p>
            </div>
            <button
                type="button"
                @click="menuOpen = !menuOpen"
                class="h-9 w-9 rounded-lg border border-slate-200 bg-white flex items-center justify-center text-slate-600 hover:text-primary hover:border-primary transition-colors"
                aria-label="Buka menu"
            >
                <span
                    class="material-symbols-outlined text-[20px] transition-transform duration-200"
                    :class="menuOpen ? 'rotate-180' : 'rotate-0'"
                >expand_more</span>
            </button>

            <div
                x-cloak
                x-show="menuOpen"
                @click.away="menuOpen = false"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 -translate-y-2 scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                x-transition:leave-end="opacity-0 -translate-y-1 scale-95"
                class="absolute right-0 top-14 w-56 bg-white border border-slate-200 rounded-xl shadow-lg py-2 z-50"
            >
                @if($role === 'organizer')
                    <a href="{{ route('organizer.balance') }}" class="block px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 hover:text-primary transition-colors">Saldo</a>
                    <a href="{{ route('organizer.events.index') }}" class="block px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 hover:text-primary transition-colors">Event Saya</a>
                    <a href="{{ route('organizer.events.create') }}" class="block px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 hover:text-primary transition-colors">Buat Event</a>
                    <div class="my-1 border-t border-slate-100"></div>
                @endif
                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 hover:text-primary transition-colors">Profile</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left px-4 py-2 text-sm font-semibold text-red-600 hover:bg-red-50 transition-colors">Logout</button>
                </form>
            </div>
        </div>
    </div>
</header>
