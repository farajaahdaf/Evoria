<!DOCTYPE html>
<html class="light" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Konser Terbaru - {{ config('app.name', 'Evoria') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/favicon-icon.png') }}">

    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet"/>
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
                    <x-event-search :initial-value="request('q', '')" />
                </div>
                
                <!-- Actions -->
                <div class="flex items-center gap-4">
                    <a href="#" class="hidden sm:flex items-center gap-2 text-[14px] font-bold text-slate-900 hover:text-primary transition-colors">
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

    <main class="max-w-[1200px] mx-auto px-6 py-10 space-y-10">
        
        <!-- Header Text -->
        <section>
            @php
                $categoryName = $category ? $category->name : 'Semua Kategori';
            @endphp
            <p class="text-[11px] font-black tracking-widest text-[#2563EB] uppercase mb-2">KATEGORI TERPILIH</p>
            <h1 class="text-[40px] font-black text-black leading-tight tracking-tight mb-3">Daftar {{ $categoryName }} Terbaru</h1>
            <p class="text-slate-500 text-[15px] font-medium max-w-2xl leading-relaxed">
                Temukan kurasi event terbaik dari artis lokal hingga mancanegara untuk kategori {{ $categoryName }}.<br>
                Pengalaman hiburan kelas dunia menanti Anda.
            </p>
        </section>

        <!-- Filter Bar -->
        <section>
            @php
                $currentSort = $sort ?? 'latest';
                $currentCity = $city ?? '';
                $filterOptions = [
                    'latest'     => ['label' => 'Terbaru',         'icon' => 'schedule'],
                    'price_desc' => ['label' => 'Harga Tertinggi', 'icon' => 'arrow_upward'],
                    'price_asc'  => ['label' => 'Harga Terendah',  'icon' => 'arrow_downward'],
                ];
            @endphp
            <div class="flex flex-col gap-4 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm md:flex-row md:items-center md:justify-between">
                <div class="flex items-center gap-3 flex-wrap">
                    <span class="text-[13px] font-bold text-slate-500 mr-1">Urutkan:</span>
                    @foreach($filterOptions as $value => $option)
                        @php
                            $isActive = $currentSort === $value;
                        @endphp
                        <a href="{{ request()->fullUrlWithQuery(['sort' => $value]) }}"
                           class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full text-[13px] font-bold border transition-all duration-200
                                  {{ $isActive
                                      ? 'bg-[#2563EB] text-white border-[#2563EB] shadow-md shadow-blue-200'
                                      : 'bg-white text-slate-600 border-slate-200 hover:border-[#2563EB] hover:text-[#2563EB]' }}">
                            <span class="material-symbols-outlined text-[15px]">{{ $option['icon'] }}</span>
                            {{ $option['label'] }}
                        </a>
                    @endforeach
                </div>

                <form action="{{ url()->current() }}" method="GET" class="flex w-full flex-col gap-2 sm:flex-row md:max-w-md">
                    <input type="hidden" name="sort" value="{{ $currentSort }}">
                    <div class="relative flex-1">
                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-[20px]">location_on</span>
                        <input
                            type="search"
                            name="city"
                            value="{{ $currentCity }}"
                            list="available-cities"
                            placeholder="Cari kota..."
                            autocomplete="off"
                            class="h-11 w-full rounded-full border border-slate-200 bg-slate-50 pl-11 pr-4 text-[13px] font-semibold text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-[#2563EB] focus:bg-white focus:ring-4 focus:ring-blue-100"
                        >
                        <datalist id="available-cities">
                            @foreach($cityOptions as $cityOption)
                                <option value="{{ $cityOption }}"></option>
                            @endforeach
                        </datalist>
                    </div>
                    <button type="submit" class="inline-flex h-11 items-center justify-center gap-1.5 rounded-full bg-slate-900 px-5 text-[13px] font-bold text-white transition hover:bg-[#2563EB]">
                        <span class="material-symbols-outlined text-[16px]">search</span>
                        Filter
                    </button>
                </form>
            </div>

            @if($currentSort !== 'latest' || $currentCity !== '')
                <div class="mt-3 flex items-center gap-2">
                    @if($currentCity !== '')
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-blue-50 px-3 py-1.5 text-[12px] font-bold text-[#2563EB]">
                            <span class="material-symbols-outlined text-[14px]">location_on</span>
                            {{ $currentCity }}
                        </span>
                    @endif
                    <a href="{{ url()->current() }}"
                       class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full text-[12px] font-bold text-slate-400 hover:text-red-500 border border-transparent hover:border-red-200 transition-all duration-200">
                        <span class="material-symbols-outlined text-[14px]">close</span>
                        Reset Filter
                    </a>
                </div>
            @endif
        </section>

        <!-- Events Grid -->
        <section>
            @php
                $resolveBannerUrl = function ($path, $fallback) {
                    if (blank($path)) return $fallback;
                    if (\Illuminate\Support\Str::startsWith($path, ['http://', 'https://'])) return $path;
                    $normalizedPath = ltrim(preg_replace('#^/?storage/#', '', $path), '/');
                    return \Illuminate\Support\Facades\Storage::url($normalizedPath);
                };
            @endphp

            @if($events->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($events as $index => $event)
                        @php
                            $minPrice = $event->tickets->count() > 0 ? $event->tickets->min('price') : 0;
                            // Make the first item take 2 columns for highlighting (as in original design)
                            $isFeatured = $index === 0 && $events->currentPage() === 1;
                        @endphp
                        
                        <a href="{{ route('events.show', $event->slug ?? $event->id) }}" class="bg-white rounded-[16px] overflow-hidden {{ $isFeatured ? 'lg:col-span-2' : '' }} group flex flex-col shadow-[0_2px_15px_rgba(0,0,0,0.03)] border border-slate-100 hover:shadow-lg transition-all duration-300">
                            <div class="relative {{ $isFeatured ? 'h-[280px]' : 'h-[220px]' }} overflow-hidden bg-slate-200">
                                <img src="{{ $resolveBannerUrl($event->banner_path, 'https://images.unsplash.com/photo-1540039155732-68473638280f?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80') }}" alt="{{ $event->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            </div>
                            
                            <div class="p-6 flex-1 flex flex-col">
                                @if($isFeatured)
                                    <div class="flex justify-between items-start mb-2">
                                        <span class="text-[11px] font-bold text-red-600 tracking-wider uppercase">TRENDING EVENT</span>
                                        <span class="text-[11px] font-bold text-slate-400">Mulai Dari</span>
                                    </div>
                                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-4 mb-4">
                                        <h3 class="font-black text-[24px] sm:text-[28px] text-slate-900 leading-tight group-hover:text-primary transition-colors line-clamp-2">{{ $event->title }}</h3>
                                        <p class="text-[20px] sm:text-[24px] font-black text-[#2563EB] shrink-0">
                                            {{ $minPrice > 0 ? 'Rp ' . number_format($minPrice, 0, ',', '.') : 'Gratis' }}
                                        </p>
                                    </div>
                                @else
                                    <span class="text-[11px] font-bold text-red-600 tracking-wider uppercase mb-2 block line-clamp-1">{{ $categoryName }}</span>
                                    <h3 class="font-black text-[18px] text-slate-900 leading-tight mb-4 group-hover:text-primary transition-colors line-clamp-2">{{ $event->title }}</h3>
                                @endif
                                
                                <div class="space-y-2 mb-6 {{ $isFeatured ? 'flex flex-col sm:flex-row sm:items-center sm:gap-6 sm:space-y-0' : '' }}">
                                    <div class="flex items-center gap-2 {{ $isFeatured ? 'text-[14px]' : 'text-[13px]' }} font-medium text-slate-500">
                                        <span class="material-symbols-outlined text-[16px]">calendar_today</span>
                                        {{ \Carbon\Carbon::parse($event->start_time)->translatedFormat('d M Y') }}
                                    </div>
                                    <div class="flex items-center gap-2 {{ $isFeatured ? 'text-[14px]' : 'text-[13px]' }} font-medium text-slate-500">
                                        <span class="material-symbols-outlined text-[16px]">location_on</span>
                                        <span class="line-clamp-1">{{ $event->location_name }}</span>
                                    </div>
                                </div>
                                
                                <div class="mt-auto flex items-center justify-between border-t border-slate-100 pt-5">
                                    @if($isFeatured)
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center font-bold text-[10px] text-slate-600 uppercase">
                                                {{ substr($event->organizer->name ?? 'OP', 0, 2) }}
                                            </div>
                                            <span class="text-[13px] font-bold text-slate-800 line-clamp-1 max-w-[120px]">{{ $event->organizer->name ?? 'Organizer' }}</span>
                                        </div>
                                        <button class="bg-[#2563EB]/10 text-[#2563EB] hover:bg-[#2563EB] hover:text-white px-6 py-2.5 rounded-lg text-[14px] font-bold transition-colors shrink-0">Pesan Sekarang</button>
                                    @else
                                        <p class="text-[18px] font-black text-slate-900">
                                            {{ $minPrice > 0 ? 'Rp ' . number_format($minPrice, 0, ',', '.') : 'Gratis' }}
                                        </p>
                                        <span class="text-[11px] font-bold text-slate-400 uppercase line-clamp-1 max-w-[100px] text-right">{{ $event->organizer->name ?? 'Organizer' }}</span>
                                    @endif
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
                
                <!-- Pagination (Tailwind Pagination) -->
                @if ($events->hasPages())
                    <div class="mt-12 flex justify-center">
                        {{ $events->links('pagination::tailwind') }}
                    </div>
                @endif
                
            @else
                <!-- Empty State -->
                <div class="bg-white rounded-[16px] h-[320px] flex flex-col items-center justify-center shadow-[0_2px_10px_rgba(0,0,0,0.02)] border border-slate-100 text-center px-6">
                    <span class="material-symbols-outlined text-slate-300 text-[64px] mb-4">search_off</span>
                    <p class="text-[20px] font-bold text-slate-800 mb-2">Belum ada event tersedia</p>
                    <p class="text-[15px] font-medium text-slate-500">
                        @if($currentCity !== '')
                            Tidak ada event {{ $categoryName }} di {{ $currentCity }}. Coba kota lain yang tersedia di pencarian.
                        @else
                            Belum ada listing event untuk kategori ini. Coba periksa kembali nanti.
                        @endif
                    </p>
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
            Males cari event? Tanya aja ke Evoria AI!
        </div>
        <button @click="chatbotOpen = !chatbotOpen" class="size-16 bg-gradient-to-tr from-primary to-[#4F46E5] text-white rounded-[24px] flex items-center justify-center shadow-2xl hover:scale-110 active:scale-95 transition-all duration-200 group relative">
            <span x-show="!chatbotOpen" class="material-symbols-outlined text-3xl transition-transform group-hover:rotate-12">smart_toy</span>
            <span x-cloak x-show="chatbotOpen" class="material-symbols-outlined text-3xl transition-transform">close</span>
            <div class="absolute inset-0 rounded-[24px] bg-white/20 opacity-0 group-hover:opacity-100 transition-opacity"></div>
        </button>
    </div>

    <!-- AI Chatbot Popover Panel -->
    <div x-cloak x-show="chatbotOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-8 scale-95" x-transition:enter-end="opacity-100 translate-y-0 scale-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 scale-100" x-transition:leave-end="opacity-0 translate-y-8 scale-95" class="fixed bottom-28 right-8 w-[380px] h-[550px] bg-white rounded-[24px] shadow-2xl border border-slate-100 flex flex-col z-[55] overflow-hidden" x-data="chatBox()">
        <div class="bg-gradient-to-r from-primary to-[#4F46E5] p-5 text-white flex items-center gap-3">
            <div class="size-10 bg-white/20 backdrop-blur-md rounded-xl flex items-center justify-center">
                <span class="material-symbols-outlined text-white">robot_2</span>
            </div>
            <div>
                <h4 class="font-extrabold text-base leading-tight">Evoria AI Assistant</h4>
                <p class="text-white/80 text-xs">Asisten tiket cerdas Anda</p>
            </div>
        </div>
        
        <div class="flex-1 bg-slate-50 p-5 overflow-y-auto space-y-5" id="chat-messages">
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

        <div class="p-4 border-t border-slate-100 bg-white">
            <form @submit.prevent="sendMessage" class="relative">
                <input type="text" x-model="newMessage" placeholder="Ketik pencarian Anda..." class="w-full h-12 bg-slate-100 border-none rounded-full pl-5 pr-14 focus:outline-none focus:ring-2 focus:ring-primary/50 text-sm text-slate-700 placeholder:text-slate-400">
                <button type="submit" :disabled="loading || !newMessage.trim()" class="absolute right-1 top-1 size-10 bg-primary text-white rounded-full flex items-center justify-center hover:bg-primary/90 transition-colors disabled:opacity-50 disabled:bg-slate-300">
                    <span class="material-symbols-outlined text-[20px] ml-0.5">send</span>
                </button>
            </form>
        </div>
    </div>

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
                        const res = await fetch('{{ route('chat') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ prompt: prompt })
                        });
                        const data = await res.json();

                        // TEMP CHATBOT DEBUG: hapus blok ini setelah selesai inspect response JSON di browser console.
                        console.log('[Chatbot Debug] HTTP status:', res.status);
                        console.log('[Chatbot Debug] Response JSON:', data);
                        
                        this.messages.push({ role: 'assistant', content: data.response });
                    } catch (e) {
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
                    html = html.replace(/\[((?:[^\[\]]|\[[^\]]*\])*)\]\((https?:\/\/[^)\s]+)\)/g, '<a href="$2" target="_blank" rel="noopener noreferrer" class="text-primary hover:underline font-bold">$1</a>');
                    html = html.replace(/(^|[\s>])(https?:\/\/[^\s<]+)/g, '$1<a href="$2" target="_blank" rel="noopener noreferrer" class="text-primary hover:underline font-bold">$2</a>');
                    html = html.replace(/\n/g, '<br>');
                    return html;
                }
            }));
        });
    </script>

</body>
</html>
