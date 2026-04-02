<!DOCTYPE html>
<html class="light" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Evoria') }} - Beranda</title>
    
    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    
    <!-- Alpine JS -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Tailwind CSS (Vite) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Temporary Tailwind Script for precise color fallback, though Vite app.css will take over -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#2563EB",
                        "amber-accent": "#f59e0b",
                        "background-light": "#F8FAFC",
                        "surface": {
                            "light": "#FFFFFF"
                        }
                    },
                    fontFamily: {
                        "sans": ["Plus Jakarta Sans", "sans-serif"],
                        "display": ["Plus Jakarta Sans", "sans-serif"]
                    },
                    borderRadius: {
                        "DEFAULT": "1.5rem",
                        "lg": "1.5rem",
                        "xl": "1.5rem",
                        "full": "9999px"
                    },
                },
            },
        }
    </script>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .hide-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .hide-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        .fab-container:hover .fab-tooltip {
            opacity: 1;
            transform: translateX(-110%) translateY(-50%);
        }
    </style>
</head>
<body class="bg-background-light text-slate-900 min-h-screen" x-data="{ chatbotOpen: false }">

    <!-- Navigasi Atas -->
    <header class="sticky top-0 z-50 w-full border-b border-slate-200 bg-white/80 backdrop-blur-md">
        <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between gap-8">
            <!-- Logo -->
            <a href="/" class="flex items-center gap-2 shrink-0">
                <div class="size-10 bg-primary rounded-xl flex items-center justify-center text-white">
                    <span class="material-symbols-outlined text-2xl">confirmation_number</span>
                </div>
                <h1 class="text-2xl font-extrabold tracking-tight text-slate-900">Evoria</h1>
            </a>
            
            <!-- Link Navigasi -->
            <nav class="hidden lg:flex items-center gap-8">
                @foreach($categories->take(4) as $cat)
                    <a class="text-sm font-semibold hover:text-primary transition-colors" href="#">{{ $cat->name }}</a>
                @endforeach
            </nav>
            
            <!-- Bar Pencarian -->
            <div class="flex-1 max-w-md hidden md:block">
                <form action="/" method="GET" class="relative group">
                    <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-primary transition-colors">search</span>
                    <input name="search" class="w-full h-12 pl-12 pr-4 bg-slate-100 border-none rounded-full focus:ring-2 focus:ring-primary/50 text-sm placeholder:text-slate-500" placeholder="Cari event, artis, atau lokasi..." type="text"/>
                </form>
            </div>
            
            <!-- Aksi -->
            <div class="flex items-center gap-4">
                @if (Route::has('login'))
                    @auth
                        @if(Auth::user()->role === 'organizer')
                            <a href="{{ route('organizer.events.create') }}" class="hidden sm:flex items-center gap-2 px-5 py-2.5 bg-primary text-white text-sm font-bold rounded-full hover:bg-primary/90 transition-all shadow-lg shadow-primary/20">
                                <span class="material-symbols-outlined text-sm">add</span>
                                <span>Buat Event</span>
                            </a>
                        @endif
                        <a href="{{ url('/dashboard') }}" class="flex items-center gap-2">
                            <div class="size-11 rounded-full bg-slate-200 overflow-hidden border-2 border-primary/20 hover:border-primary transition duration-300">
                                <img alt="User profile avatar" class="w-full h-full object-cover" src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=2563EB&color=fff"/>
                            </div>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-bold text-slate-600 hover:text-primary transition-colors">Log in</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="px-5 py-2.5 bg-primary text-white text-sm font-bold rounded-full hover:bg-primary/90 transition-all shadow-lg shadow-primary/20">Masuk / Daftar</a>
                        @endif
                    @endauth
                @endif
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-6 py-8 space-y-12">
        <!-- Hero Carousel -->
        <section class="relative rounded-[24px] overflow-hidden aspect-[21/9] bg-slate-800 group">
            <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('https://images.unsplash.com/photo-1470229722913-7c092b122fba?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80')">
                <div class="absolute inset-0 bg-gradient-to-r from-slate-900/90 via-slate-900/50 to-transparent"></div>
            </div>
            <div class="relative h-full flex flex-col justify-center px-12 max-w-2xl">
                <span class="inline-flex items-center gap-2 px-3 py-1 bg-primary/20 backdrop-blur-md text-primary text-xs font-bold uppercase tracking-wider rounded-full mb-6 w-fit border border-primary/30">
                    <span class="size-2 bg-primary rounded-full animate-pulse"></span>
                    Event Pilihan Teratas
                </span>
                <h2 class="text-4xl md:text-5xl font-extrabold text-white leading-tight mb-4">Temukan Pengalaman Luar Biasa</h2>
                <p class="text-lg text-slate-300 mb-8 line-clamp-2">Dari konser berskala besar hingga workshop eksklusif, dapatkan tiket Anda melalui Evoria dengan aman dan cepat.</p>
                <div class="flex items-center gap-4">
                    <a href="#events" class="px-8 py-4 bg-primary text-white font-bold rounded-full hover:scale-105 transition-transform shadow-lg shadow-primary/30">
                        Eksplorasi Sekarang
                    </a>
                </div>
            </div>
        </section>

        <!-- Kategori -->
        <section class="flex flex-col gap-4">
            <h3 class="text-xl font-extrabold text-slate-900">Telusuri Kategori</h3>
            <div class="flex items-center gap-3 overflow-x-auto hide-scrollbar pb-2">
                <a href="/" class="px-6 py-3 bg-primary text-white rounded-full font-semibold flex items-center gap-2 shrink-0 shadow-sm border border-transparent">
                    <span class="material-symbols-outlined text-lg">explore</span> Semua Event
                </a>
                @foreach($categories as $category)
                    <a href="/?category_id={{ $category->id }}" class="px-6 py-3 bg-white text-slate-600 border border-slate-200 hover:bg-slate-50 hover:border-slate-300 rounded-full font-semibold flex items-center gap-2 shrink-0 transition-all shadow-sm">
                        <span class="material-symbols-outlined text-lg">{{ $category->id % 2 == 0 ? 'music_note' : 'festival' }}</span> {{ $category->name }}
                    </a>
                @endforeach
            </div>
        </section>

        <!-- Event Mendatang -->
        <section class="space-y-6" id="events">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-2xl font-extrabold text-slate-900">Event Mendatang</h3>
                    <p class="text-slate-500 text-sm mt-1">Jangan sampai kehabisan tiket untuk acara-acara seru ini!</p>
                </div>
            </div>
            
            @if($events->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($events as $event)
                        <a href="{{ route('events.show', $event->slug) }}" class="flex flex-col gap-4 group cursor-pointer">
                            <div class="aspect-[4/3] rounded-[24px] overflow-hidden relative bg-slate-200 shadow-sm">
                                @if($event->banner_path)
                                    <img alt="{{ $event->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" src="{{ asset('storage/' . $event->banner_path) }}"/>
                                @else
                                    <img alt="Placeholder" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" src="https://images.unsplash.com/photo-1540575467063-178a50c2df87?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80"/>
                                @endif
                                
                                <div class="absolute top-4 left-4 bg-white/90 backdrop-blur px-3 py-1 rounded-full text-xs font-bold text-slate-800 shadow-sm">
                                    {{ $event->category->name }}
                                </div>
                            </div>
                            <div>
                                <h4 class="text-lg font-bold text-slate-900 mb-1 leading-tight group-hover:text-primary transition-colors line-clamp-2" title="{{ $event->title }}">{{ $event->title }}</h4>
                                <div class="flex flex-col gap-1 text-slate-500 text-sm mb-3">
                                    <div class="flex items-center gap-1.5 line-clamp-1">
                                        <span class="material-symbols-outlined text-[16px] text-primary">calendar_today</span>
                                        <span>{{ \Carbon\Carbon::parse($event->start_time)->translatedFormat('D, d M Y | H:i') }}</span>
                                    </div>
                                    <div class="flex items-center gap-1.5 line-clamp-1 truncate" title="{{ $event->location_name }}">
                                        <span class="material-symbols-outlined text-[16px] text-primary">location_on</span>
                                        <span>{{ $event->location_name }}</span>
                                    </div>
                                </div>
                                
                                @php
                                    $minPrice = $event->tickets->min('price');
                                @endphp
                                <p class="text-sm font-extrabold {{ $minPrice > 0 ? 'text-amber-accent' : 'text-emerald-500' }} mt-1 border-t border-slate-100 pt-3">
                                    {{ $minPrice > 0 ? 'Mulai Rp ' . number_format($minPrice, 0, ',', '.') : 'Gratis Masuk' }}
                                </p>
                            </div>
                        </a>
                    @endforeach
                </div>
                
                <!-- Pagination (if applicable) -->
                @if(method_exists($events, 'links'))
                <div class="flex justify-center pt-8">
                    {{ $events->links() }}
                </div>
                @endif
            @else
                <div class="py-20 text-center border-2 border-dashed border-slate-200 rounded-3xl bg-slate-50">
                    <span class="material-symbols-outlined text-5xl text-slate-300 mb-3 block">confirmation_number</span>
                    <h3 class="text-xl font-bold text-slate-700 mb-1">Belum ada event ditemukan</h3>
                    <p class="text-slate-500 text-sm">Coba ubah kata kunci pencarian atau kategori Anda.</p>
                </div>
            @endif
        </section>
    </main>

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

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 py-16 mt-16">
        <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 md:grid-cols-4 gap-12">
            <div class="col-span-1 md:col-span-1">
                <div class="flex items-center gap-2 mb-6">
                    <div class="size-8 bg-primary rounded-lg flex items-center justify-center text-white">
                        <span class="material-symbols-outlined text-xl">confirmation_number</span>
                    </div>
                    <h1 class="text-xl font-extrabold tracking-tight text-slate-900">Evoria</h1>
                </div>
                <p class="text-slate-500 text-sm leading-relaxed">
                    Evoria adalah pasar global terkemuka untuk pengalaman langsung yang luar biasa. Temukan dan pesan tiket konser, festival, dan workshop terbaik.
                </p>
            </div>
            <div>
                <h5 class="font-bold mb-6 text-slate-900">Tautan Cepat</h5>
                <ul class="space-y-4 text-sm text-slate-500">
                    <li><a class="hover:text-primary transition-colors" href="#">Cari Event</a></li>
                    <li><a class="hover:text-primary transition-colors" href="#">Buat Event</a></li>
                </ul>
            </div>
            <div>
                <h5 class="font-bold mb-6 text-slate-900">Dukungan</h5>
                <ul class="space-y-4 text-sm text-slate-500">
                    <li><a class="hover:text-primary transition-colors" href="#">Pusat Bantuan</a></li>
                    <li><a class="hover:text-primary transition-colors" href="#">Hubungi Kami</a></li>
                </ul>
            </div>
            <div>
                <h5 class="font-bold mb-6 text-slate-900">Berlangganan</h5>
                <p class="text-sm text-slate-500 mb-4">Dapatkan info konser terbaru setiap minggunya.</p>
                <div class="flex gap-2">
                    <input class="flex-1 h-11 px-4 bg-slate-100 border-none rounded-full text-sm" placeholder="Email Anda..." type="email"/>
                    <button class="size-11 bg-primary text-white rounded-full flex items-center justify-center hover:bg-primary/90 transition-colors">
                        <span class="material-symbols-outlined text-sm">arrow_forward</span>
                    </button>
                </div>
            </div>
        </div>
        <div class="max-w-7xl mx-auto px-6 pt-8 mt-12 border-t border-slate-200 flex flex-col md:flex-row justify-between items-center gap-4">
            <p class="text-sm text-slate-500">© 2026 Evoria Inc. Hak cipta dilindungi.</p>
        </div>
    </footer>

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
