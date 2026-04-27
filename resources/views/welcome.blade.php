<!DOCTYPE html>
<html class="light" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Evoria') }} - Beranda</title>
    
    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    
    <!-- Tailwind CSS (Vite) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#2563EB",
                        "hero-bg": "#F8F9FA",
                        "dark-footer": "#0F172A",
                    },
                    fontFamily: {
                        "sans": ["Plus Jakarta Sans", "sans-serif"],
                    },
                },
            },
        }
    </script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        .fab-container:hover .fab-tooltip { opacity: 1; transform: translateX(-110%) translateY(-50%); }
    </style>
</head>
<body class="bg-[#ebebeb] min-h-screen text-slate-900" x-data="{ chatbotOpen: false }">

    @if(auth()->check() && auth()->user()->role === 'attendee')
        <x-attendee-main-header />
    @elseif(auth()->check() && auth()->user()->role === 'organizer')
        <x-organizer-main-header />
    @else
        <!-- Navigasi Atas (header utama default dengan Buat Event / Daftar / Masuk) -->
        <header class="bg-white border-b border-gray-200">
            <div class="max-w-[1400px] mx-auto px-6 h-[80px] flex items-center justify-between gap-6">
                
                <div class="flex items-center gap-8">
                    <!-- Logo -->
                    <a href="/" class="flex items-center gap-1 shrink-0">
                        <x-application-logo class="h-10 w-auto" />
                    </a>
                    
                    <!-- Links -->
                    <nav class="hidden lg:flex items-center gap-6">
                        <a class="text-[14px] font-bold text-slate-900 hover:text-primary transition-colors" href="#">Beli Tiket</a>
                        <a class="text-[14px] font-bold text-slate-900 hover:text-primary transition-colors" href="#">Sponsor</a>
                        <a class="text-[14px] font-bold text-slate-900 hover:text-primary transition-colors" href="#">Bantuan</a>
                    </nav>
                </div>
                
                <!-- Search -->
                <div class="flex-1 max-w-[500px] hidden md:block">
                    <x-event-search :initial-value="$search ?? ''" />
                </div>
                
                <!-- Actions -->
                <div class="flex items-center gap-4">
                    @php
                        $createEventUrl = route('register.organizer');

                        if (auth()->check()) {
                            if (auth()->user()->role === 'organizer') {
                                $createEventUrl = optional(auth()->user()->organizerProfile)->status === 'verified'
                                    ? route('organizer.events.create')
                                    : route('organizer.pending');
                            } else {
                                $createEventUrl = route('dashboard');
                            }
                        }
                    @endphp

                    <a href="{{ $createEventUrl }}" class="hidden sm:flex items-center gap-2 text-[14px] font-bold text-slate-900 hover:text-primary transition-colors">
                        <span class="material-symbols-outlined rounded-md bg-white text-[22px]">calendar_add_on</span>
                        Buat Event
                    </a>
                    
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="px-6 py-[10px] text-[14px] font-bold border border-primary text-primary rounded-lg hover:bg-primary/5 transition-colors">Dashboard</a>
                        @else
                            <div class="flex items-center gap-3 border-l border-slate-200 pl-4 ml-2">
                                <a href="{{ route('register') }}" class="px-6 py-[10px] text-[14px] font-bold border border-primary text-primary rounded-lg hover:bg-primary/5 transition-colors">Daftar</a>
                                <a href="{{ route('login') }}" class="px-6 py-[10px] bg-primary text-white text-[14px] font-bold rounded-lg hover:bg-primary/90 transition-all shadow-sm">Masuk</a>
                            </div>
                        @endauth
                    @endif
                </div>
            </div>
        </header>
    @endif

    <main class="max-w-[1200px] mx-auto px-6 py-10 space-y-12">
        
        <!-- Hero Section / Banner Carousel -->
        <section>
            @php
                $resolveBannerUrl = function ($path, $fallback) {
                    if (blank($path)) {
                        return $fallback;
                    }
                    if (\Illuminate\Support\Str::startsWith($path, ['http://', 'https://'])) {
                        return $path;
                    }
                    $normalizedPath = ltrim(preg_replace('#^/?storage/#', '', $path), '/');
                    return \Illuminate\Support\Facades\Storage::url($normalizedPath);
                };

                $fallbackImages = [
                    'https://images.unsplash.com/photo-1470229722913-7c092b122fba?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80',
                    'https://images.unsplash.com/photo-1540575467063-178a50c2df87?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80',
                    'https://images.unsplash.com/photo-1501281668745-f7f57925c3b4?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80',
                    'https://images.unsplash.com/photo-1459749411175-04bf5292ceea?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80',
                    'https://images.unsplash.com/photo-1429962714451-bb934ecdc4ec?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80',
                ];

                $bannerEvents = isset($events) && count($events) > 0 ? $events->take(5) : collect();
            @endphp

            @if($bannerEvents->count() > 0)
                <div
                    class="relative rounded-[20px] overflow-hidden bg-slate-900 shadow-lg h-[360px] select-none group"
                    x-data="{
                        current: 0,
                        total: {{ $bannerEvents->count() }},
                        autoTimer: null,
                        paused: false,
                        slides: {{ json_encode($bannerEvents->values()->map(fn($e, $i) => [
                            'url'      => route('events.show', $e->slug ?? $e->id),
                            'img'      => $resolveBannerUrl($e->banner_path, $fallbackImages[$loop->index ?? $i] ?? $fallbackImages[0]),
                            'title'    => $e->title,
                            'location' => $e->location_name,
                            'date'     => \Carbon\Carbon::parse($e->start_time)->translatedFormat('d M Y'),
                        ])->values()) }},
                        init() {
                            this.startAuto();
                        },
                        startAuto() {
                            this.autoTimer = setInterval(() => {
                                if (!this.paused) this.next();
                            }, 5000);
                        },
                        next() { this.current = (this.current + 1) % this.total; },
                        prev() { this.current = (this.current - 1 + this.total) % this.total; },
                        goTo(i) { this.current = i; }
                    }"
                    @mouseenter="paused = true"
                    @mouseleave="paused = false"
                >
                    {{-- Slides --}}
                    <template x-for="(slide, index) in slides" :key="index">
                        <a
                            :href="slide.url"
                            class="absolute inset-0 w-full h-full block transition-opacity duration-700"
                            :class="{ 'opacity-100 z-10': current === index, 'opacity-0 z-0': current !== index }"
                        >
                            <img
                                :src="slide.img"
                                :alt="slide.title"
                                class="absolute inset-0 w-full h-full object-cover transition-transform duration-[8000ms]"
                                :class="{ 'scale-110': current === index, 'scale-100': current !== index }"
                            >
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-900/90 via-slate-900/40 to-transparent"></div>
                            <div class="absolute bottom-0 left-0 p-8 w-full z-10 flex flex-col">
                                <span class="px-3 py-1 bg-primary/80 backdrop-blur text-white text-[10px] font-bold uppercase tracking-wider rounded-md mb-3 w-fit">Event Unggulan</span>
                                <h2 class="text-[28px] md:text-[38px] font-black text-white leading-tight mb-2 line-clamp-2" x-text="slide.title"></h2>
                                <div class="flex items-center gap-4 text-slate-200 text-[13px] font-medium">
                                    <span class="flex items-center gap-1.5">
                                        <span class="material-symbols-outlined text-[15px]">location_on</span>
                                        <span x-text="slide.location"></span>
                                    </span>
                                    <span class="flex items-center gap-1.5">
                                        <span class="material-symbols-outlined text-[15px]">calendar_today</span>
                                        <span x-text="slide.date"></span>
                                    </span>
                                </div>
                            </div>
                        </a>
                    </template>

                    {{-- Arrow Prev --}}
                    <button
                        @click.prevent="prev()"
                        class="absolute left-4 top-1/2 -translate-y-1/2 z-20 w-10 h-10 rounded-full bg-black/40 backdrop-blur hover:bg-black/60 text-white flex items-center justify-center transition-all duration-300 hover:scale-110 focus:outline-none opacity-0 group-hover:opacity-100"
                    >
                        <span class="material-symbols-outlined text-[22px]">chevron_left</span>
                    </button>

                    {{-- Arrow Next --}}
                    <button
                        @click.prevent="next()"
                        class="absolute right-4 top-1/2 -translate-y-1/2 z-20 w-10 h-10 rounded-full bg-black/40 backdrop-blur hover:bg-black/60 text-white flex items-center justify-center transition-all duration-300 hover:scale-110 focus:outline-none opacity-0 group-hover:opacity-100"
                    >
                        <span class="material-symbols-outlined text-[22px]">chevron_right</span>
                    </button>

                    {{-- Dot Indicators --}}
                    <div class="absolute bottom-5 right-8 z-20 flex items-center gap-2">
                        <template x-for="(slide, index) in slides" :key="'dot-' + index">
                            <button
                                @click.prevent="goTo(index)"
                                class="rounded-full transition-all duration-300 focus:outline-none"
                                :class="current === index
                                    ? 'w-6 h-2.5 bg-white'
                                    : 'w-2.5 h-2.5 bg-white/40 hover:bg-white/70'"
                            ></button>
                        </template>
                    </div>

                    {{-- Slide Counter --}}
                    <div class="absolute top-5 right-5 z-20 bg-black/40 backdrop-blur text-white text-[12px] font-bold px-3 py-1 rounded-full transition-opacity duration-300 opacity-0 group-hover:opacity-100">
                        <span x-text="current + 1"></span> / <span x-text="total"></span>
                    </div>
                </div>
            @else
                <div class="bg-white rounded-[16px] h-[360px] flex items-center justify-center shadow-[0_2px_10px_rgba(0,0,0,0.02)]">
                    <p class="text-[24px] italic font-medium text-slate-800">Belum ada event unggulan saat ini</p>
                </div>
            @endif
        </section>

        <!-- Featured Events -->
        <section>
            <div class="mb-6 flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                <h2 class="text-[28px] font-bold text-black tracking-tight">
                    {{ filled($search ?? '') ? 'Hasil Pencarian' : 'Featured Events' }}
                </h2>
                @if(filled($search ?? ''))
                    <a href="{{ route('home') }}" class="text-[13px] font-bold text-primary hover:underline">Reset pencarian</a>
                @endif
            </div>
            
            @if(isset($events) && count($events) > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($events as $event)
                        <a href="{{ route('events.show', $event->slug ?? $event->id) }}" class="bg-white rounded-[16px] border border-slate-100 overflow-hidden shadow-sm hover:shadow-md transition-shadow group flex flex-col">
                            <div class="relative h-40 overflow-hidden bg-slate-200">
                                <img src="{{ $resolveBannerUrl($event->banner_path, 'https://images.unsplash.com/photo-1540575467063-178a50c2df87?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80') }}" alt="{{ $event->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            </div>
                            <div class="p-4 flex-1 flex flex-col">
                                <h3 class="font-bold text-[15px] text-slate-900 line-clamp-2 leading-tight group-hover:text-primary transition-colors">{{ $event->title }}</h3>
                                <p class="text-[13px] font-medium text-slate-500 mt-2 flex items-center gap-1.5 line-clamp-1">
                                    <span class="material-symbols-outlined text-[16px]">calendar_today</span>
                                    {{ \Carbon\Carbon::parse($event->start_time)->translatedFormat('d M Y') }}
                                </p>
                                <div class="mt-auto pt-4 flex items-center justify-between border-t border-slate-100">
                                    @php
                                        $minPrice = $event->tickets && $event->tickets->count() > 0 ? $event->tickets->min('price') : 0;
                                    @endphp
                                    <p class="text-[15px] font-black {{ $minPrice > 0 ? 'text-slate-900' : 'text-primary' }}">
                                        {{ $minPrice > 0 ? 'Rp ' . number_format($minPrice, 0, ',', '.') : 'Gratis' }}
                                    </p>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="bg-white rounded-[16px] h-[320px] flex items-center justify-center shadow-[0_2px_10px_rgba(0,0,0,0.02)]">
                    <p class="text-[24px] italic font-medium text-slate-800">
                        {{ filled($search ?? '') ? 'Event tidak ditemukan' : 'Belum ada event untuk saat ini' }}
                    </p>
                </div>
            @endif
        </section>

        <!-- Kategori Event -->
        <section>
            <h2 class="text-[28px] font-bold text-black mb-6 tracking-tight">Kategori Event</h2>
            
            @if(isset($categories) && $categories->count() > 0)
                @php
                    // Mapping icon berdasarkan keyword nama kategori (English & Indonesian)
                    $iconMap = [
                        // Music / Concert
                        'music'        => 'local_activity',
                        'concert'      => 'local_activity',
                        'konser'       => 'local_activity',
                        'musik'        => 'local_activity',
                        // Tech / Conference
                        'tech'         => 'computer',
                        'conference'   => 'groups',
                        'teknologi'    => 'computer',
                        // Workshop
                        'workshop'     => 'build',
                        // Sports
                        'sport'        => 'sports_soccer',
                        'olahraga'     => 'sports_soccer',
                        // Art / Exhibition
                        'art'          => 'palette',
                        'exhibition'   => 'photo_library',
                        'pameran'      => 'photo_library',
                        'seni'         => 'palette',
                        // Festival
                        'festival'     => 'celebration',
                        // Seminar
                        'seminar'      => 'campaign',
                        // Pertunjukan
                        'pertunjukan'  => 'theater_comedy',
                        'penampilan'   => 'theater_comedy',
                        // Travel / Tour
                        'tur'          => 'luggage',
                        'perjalanan'   => 'luggage',
                        'travel'       => 'luggage',
                        // Social
                        'social'       => 'groups',
                        'gathering'    => 'groups',
                        'gethering'    => 'groups',
                        // Food
                        'kuliner'      => 'restaurant',
                        'food'         => 'restaurant',
                        // Education
                        'pendidikan'   => 'school',
                        'education'    => 'school',
                        // Business
                        'bisnis'       => 'business_center',
                        'business'     => 'business_center',
                        // Film
                        'film'         => 'movie',
                    ];

                    $getIcon = function($name) use ($iconMap) {
                        $lower = strtolower($name);
                        foreach ($iconMap as $keyword => $icon) {
                            if (str_contains($lower, $keyword)) return $icon;
                        }
                        return 'event'; // default icon
                    };
                @endphp

                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-4">
                    @foreach($categories as $cat)
                        <a href="{{ route('category.show', ['slug' => $cat->slug ?? \Illuminate\Support\Str::slug($cat->name)]) }}" class="block bg-white border border-slate-200 rounded-[16px] py-6 px-2 flex flex-col items-center justify-center gap-3 cursor-pointer hover:border-primary hover:shadow-md transition-all focus:outline-none focus:ring-2 focus:ring-primary group">
                            <span class="material-symbols-outlined text-[42px] text-slate-800 font-light group-hover:text-primary transition-colors">{{ $getIcon($cat->name) }}</span>
                            <p class="text-[13px] font-bold text-slate-800 text-center leading-tight max-w-[100px] group-hover:text-primary transition-colors">{{ $cat->name }}</p>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="bg-white rounded-[16px] h-[120px] flex items-center justify-center border border-slate-100 text-slate-400 text-[15px] font-medium">
                    Belum ada kategori tersedia.
                </div>
            @endif
        </section>

    </main>

    <!-- Footer -->
    <footer class="bg-[#111827] mt-20 text-white">
        <div class="max-w-[1200px] mx-auto px-6 py-10 flex flex-col md:flex-row justify-between items-center gap-6">
            <div class="flex items-center gap-8">
                <a href="#" class="text-[14px] font-bold hover:text-slate-300 transition-colors">About Us</a>
                <a href="#" class="text-[14px] font-bold hover:text-slate-300 transition-colors">Carrer</a>
                <a href="#" class="text-[14px] font-bold hover:text-slate-300 transition-colors">FAQ</a>
                <a href="#" class="text-[14px] font-bold hover:text-slate-300 transition-colors">Contact</a>
            </div>
            
            <div>
                <x-application-logo class="h-10 w-auto" />
            </div>
            
            <div class="flex items-center gap-4">
                <a href="#" class="w-8 h-8 rounded-full border border-white/30 flex items-center justify-center hover:border-white transition-colors">
                    <span class="material-symbols-outlined text-[16px]">play_arrow</span>
                </a>
                <a href="#" class="w-8 h-8 rounded-full border border-white/30 flex items-center justify-center hover:border-white transition-colors text-[14px] font-bold">
                    X
                </a>
                <a href="#" class="w-8 h-8 rounded-full border border-white/30 flex items-center justify-center hover:border-white transition-colors">
                    <span class="material-symbols-outlined text-[16px]">photo_camera</span>
                </a>
                <a href="#" class="w-8 h-8 rounded-full border border-white/30 flex items-center justify-center hover:border-white transition-colors">
                    <span class="material-symbols-outlined text-[16px]">music_note</span>
                </a>
            </div>
        </div>
    </footer>

    <!-- Chatbot FAB -->
    <div class="fixed bottom-8 right-8 z-[60] fab-container">
        <div class="fab-tooltip absolute top-1/2 left-0 -translate-y-1/2 opacity-0 pointer-events-none transition-all duration-300 ease-out whitespace-nowrap bg-slate-900 text-white px-3 py-1.5 rounded-lg text-xs font-bold shadow-xl">
            Tanya Evoria AI
        </div>
        <button @click="chatbotOpen = !chatbotOpen" class="size-16 bg-gradient-to-tr from-primary to-[#4F46E5] text-white rounded-[24px] flex items-center justify-center shadow-2xl hover:scale-110 active:scale-95 transition-all duration-200 group relative">
            <span x-show="!chatbotOpen" class="material-symbols-outlined text-3xl transition-transform group-hover:rotate-12">smart_toy</span>
            <span x-cloak x-show="chatbotOpen" class="material-symbols-outlined text-3xl transition-transform">close</span>
            <div class="absolute inset-0 rounded-[24px] bg-white/20 opacity-0 group-hover:opacity-100 transition-opacity"></div>
        </button>
    </div>

    <!-- AI Chatbot Popover Panel -->
    <div x-cloak x-show="chatbotOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-8 scale-95" x-transition:enter-end="opacity-100 translate-y-0 scale-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 scale-100" x-transition:leave-end="opacity-0 translate-y-8 scale-95" class="fixed bottom-28 right-8 w-[380px] h-[550px] bg-white rounded-[24px] shadow-2xl border border-slate-100 flex flex-col z-[55] overflow-hidden" x-data="chatBox()">
        <!-- Header -->
        <div class="bg-gradient-to-r from-primary to-[#4F46E5] p-5 text-white flex items-center gap-3">
            <div class="size-10 bg-white/20 backdrop-blur-md rounded-xl flex items-center justify-center">
                <span class="material-symbols-outlined text-white">robot_2</span>
            </div>
            <div>
                <h4 class="font-extrabold text-base leading-tight">Evoria AI Assistant</h4>
                <p class="text-white/80 text-xs">Asisten tiket cerdas Anda</p>
            </div>
        </div>
        
        <!-- Messages Area -->
        <div class="flex-1 bg-slate-50 p-5 overflow-y-auto space-y-5" id="chat-messages">
            <!-- Welcome Bot Message -->
            <div class="flex gap-2">
                <div class="size-8 rounded-full bg-primary/10 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-primary text-sm">smart_toy</span>
                </div>
                <div class="bg-white border border-slate-100 text-slate-700 rounded-2xl rounded-tl-sm px-4 py-3 max-w-[85%] shadow-sm text-sm">
                    Halo! Saya Evoria AI. Coba beri perintah seperti <strong>"Cariin konser musik gratis bulan ini dong!"</strong>
                </div>
            </div>
            
            <template x-for="message in messages">
                <div :class="message.role === 'user' ? 'flex justify-end' : 'flex gap-2'">
                    <!-- Avatar for Bot -->
                    <template x-if="message.role === 'assistant'">
                        <div class="size-8 rounded-full bg-primary/10 flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-primary text-sm">smart_toy</span>
                        </div>
                    </template>
                    
                    <div :class="message.role === 'user' ? 'bg-primary text-white rounded-tr-sm' : 'bg-white border border-slate-100 text-slate-700 rounded-tl-sm prose prose-sm max-w-none prose-a:text-primary prose-a:font-bold prose-strong:text-slate-900'" 
                         class="rounded-2xl px-4 py-3 max-w-[85%] shadow-sm text-sm" x-html="formatMessage(message.content)">
                    </div>
                </div>
            </template>

            <!-- Loading Indicator -->
            <div class="flex gap-2" x-show="loading">
                <div class="size-8 rounded-full bg-primary/10 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-primary text-sm">smart_toy</span>
                </div>
                <div class="bg-white border border-slate-100 text-slate-700 rounded-2xl rounded-tl-sm px-4 py-4 max-w-[85%] shadow-sm flex items-center gap-1.5 h-10">
                    <div class="size-1.5 bg-slate-400 rounded-full animate-bounce"></div>
                    <div class="size-1.5 bg-slate-400 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                    <div class="size-1.5 bg-slate-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                </div>
            </div>
        </div>

        <!-- Input Area -->
        <div class="p-4 border-t border-slate-100 bg-white">
            <form @submit.prevent="sendMessage" class="relative">
                <input type="text" x-model="newMessage" placeholder="Ketik pencarian Anda..." class="w-full h-12 bg-slate-100 border-none rounded-full pl-5 pr-14 focus:outline-none focus:ring-2 focus:ring-primary/50 text-sm text-slate-700 placeholder:text-slate-400">
                <button type="submit" :disabled="loading || !newMessage.trim()" class="absolute right-1 top-1 size-10 bg-primary text-white rounded-full flex items-center justify-center hover:bg-primary/90 transition-colors disabled:opacity-50 disabled:bg-slate-300">
                    <span class="material-symbols-outlined text-[20px] ml-0.5">send</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Chatbot Logic -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('chatBox', () => ({
                messages: [],
                newMessage: '',
                loading: false,
                
                async sendMessage() {
                    if (!this.newMessage.trim() || this.loading) return;
                    
                    let msg = { role: 'user', content: this.newMessage };
                    this.messages.push(msg);
                    let prompt = this.newMessage;
                    this.newMessage = '';
                    this.loading = true;
                    
                    this.scrollToBottom();

                    try {
                        const res = await fetch('/chat', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ prompt: prompt })
                        });
                        const data = await res.json();
                        
                        this.messages.push({ role: 'assistant', content: data.response });
                    } catch(e) {
                        this.messages.push({ role: 'assistant', content: "Momen sibuk, koneksi AI terputus. Coba lagi." });
                    } finally {
                        this.loading = false;
                        this.scrollToBottom();
                    }
                },
                
                scrollToBottom() {
                    setTimeout(() => {
                        const container = document.getElementById('chat-messages');
                        container.scrollTop = container.scrollHeight;
                    }, 100);
                },

                formatMessage(text) {
                    if (!text) return '';
                    let html = text.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
                    html = html.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
                    html = html.replace(/\[([^\]]+)\]\(([^)]+)\)/g, '<a href="$2" class="text-primary hover:underline font-bold">$1</a>');
                    html = html.replace(/\n/g, '<br>');
                    return html;
                }
            }));
        });
    </script>
</body>
</html>
