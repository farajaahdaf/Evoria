<!DOCTYPE html>
<html class="light" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Konser Terbaru - {{ config('app.name', 'Evoria') }}</title>
    
    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    
    <!-- Alpine JS -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
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
    </style>
</head>
<body class="bg-[#F4F6F9] min-h-screen text-slate-900">

    @if(auth()->check() && auth()->user()->role === 'attendee')
        <x-attendee-main-header />
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
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-[20px]">search</span>
                        <input class="w-full h-[44px] pl-11 pr-4 bg-[#F1F3F5] border-none rounded-lg text-[13px] placeholder:text-slate-400 focus:ring-1 focus:ring-primary focus:bg-white transition-colors" placeholder="Cari event, artis, atau lokasi..." type="text"/>
                    </div>
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
                $filterOptions = [
                    'latest'     => ['label' => 'Terbaru',         'icon' => 'schedule'],
                    'price_desc' => ['label' => 'Harga Tertinggi', 'icon' => 'arrow_upward'],
                    'price_asc'  => ['label' => 'Harga Terendah',  'icon' => 'arrow_downward'],
                ];
            @endphp
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

                @if($currentSort !== 'latest')
                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'latest']) }}"
                       class="inline-flex items-center gap-1 px-3 py-2 rounded-full text-[12px] font-bold text-slate-400 hover:text-red-500 border border-transparent hover:border-red-200 transition-all duration-200 ml-1">
                        <span class="material-symbols-outlined text-[14px]">close</span>
                        Reset
                    </a>
                @endif
            </div>
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
                    <p class="text-[15px] font-medium text-slate-500">Belum ada listing event untuk kategori ini. Coba periksa kembali nanti.</p>
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

</body>
</html>
