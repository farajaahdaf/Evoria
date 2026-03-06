<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Laravel Event Marketplace') }}</title>
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
        <!-- Vite / Tailwind -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <!-- Alpine JS -->
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    </head>
    <body class="antialiased bg-gray-50 text-gray-900 font-sans" x-data="{ chatbotOpen: false }">
        
        <!-- Navbar -->
        <nav class="bg-blue-600 text-white shadow-md sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <div class="flex items-center">
                        <a href="/" class="text-2xl font-bold tracking-tight">Evo<span class="text-yellow-400">ria</span></a>
                        <div class="hidden md:block ml-10 flex space-x-6">
                            <a href="#" class="hover:text-yellow-300 transition">Concerts</a>
                            <a href="#" class="hover:text-yellow-300 transition">Festivals</a>
                            <a href="#" class="hover:text-yellow-300 transition">Exhibitions</a>
                            <a href="#" class="hover:text-yellow-300 transition">Conferences</a>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        @if (Route::has('login'))
                            @auth
                                <a href="{{ url('/dashboard') }}" class="px-4 py-2 bg-white text-blue-600 font-bold rounded-full hover:bg-gray-100 transition shadow">Dashboard</a>
                            @else
                                <a href="{{ route('login') }}" class="font-medium hover:text-yellow-300 transition">Log in</a>
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="px-5 py-2 bg-yellow-400 text-blue-900 font-bold rounded-full hover:bg-yellow-300 shadow transition">Sign up</a>
                                @endif
                            @endauth
                        @endif
                    </div>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <div class="relative bg-blue-600 overflow-hidden">
            <div class="absolute inset-0">
                <img class="w-full h-full object-cover opacity-20" src="https://images.unsplash.com/photo-1540575467063-178a50c2df87?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80" alt="Concert Background">
                <div class="absolute inset-0 bg-blue-600 mix-blend-multiply"></div>
            </div>
            <div class="relative max-w-7xl mx-auto py-24 px-4 sm:py-32 sm:px-6 lg:px-8 text-center text-white">
                <h1 class="text-4xl md:text-6xl font-extrabold tracking-tight mb-6">Discover Extraordinary Events</h1>
                <p class="mt-4 max-w-2xl text-xl text-blue-100 mx-auto mb-10">From local meetups to huge festivals, get your tickets quickly and securely.</p>
                
                <!-- Search Box -->
                <div class="max-w-3xl mx-auto bg-white rounded-lg p-2 shadow-xl flex bg-opacity-20 backdrop-filter backdrop-blur-lg border border-white/20">
                    <input type="text" placeholder="Search events, organizers, or location..." class="flex-1 rounded-l-md px-4 py-3 bg-white text-gray-900 focus:outline-none border-none focus:ring-0">
                    <select class="px-4 py-3 bg-white border-l border-gray-200 text-gray-600 md:block hidden w-48 focus:outline-none border-none focus:ring-0">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                    <button class="bg-yellow-400 text-blue-900 font-bold px-6 py-3 rounded-r-md hover:bg-yellow-300 transition">Search</button>
                </div>
            </div>
        </div>

        <!-- Featured Events -->
        <main class="max-w-7xl mx-auto py-16 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-8 text-gray-900">
                <h2 class="text-3xl font-bold">Trending Events</h2>
                <a href="#" class="text-blue-600 font-medium hover:underline">See all events →</a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse($events as $event)
                <a href="{{ route('events.show', $event->slug) }}" class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition duration-300 border border-gray-100 flex flex-col group cursor-pointer block">
                    <div class="relative h-48 bg-gray-200 overflow-hidden">
                        <div class="absolute inset-0 bg-gradient-to-tr from-blue-500 to-indigo-600 flex items-center justify-center text-white font-bold opacity-30 group-hover:scale-105 transition duration-500"></div>
                        @if($event->banner_path)
                            <img src="{{ asset('storage/' . $event->banner_path) }}" alt="{{ $event->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                        @else
                            <img src="https://images.unsplash.com/photo-1459749411175-04bf5292ceea?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Cover" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                        @endif
                        <div class="absolute top-4 left-4 bg-white text-gray-900 text-sm font-bold px-3 py-1 rounded-md shadow-md">
                            {{ $event->category->name }}
                        </div>
                    </div>
                    <div class="p-6 flex-1 flex flex-col">
                        <div class="text-blue-600 font-bold text-sm mb-2 uppercase tracking-wide">
                            {{ $event->start_time->format('D, d M | H:i') }}
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2 truncate" title="{{ $event->title }}">{{ $event->title }}</h3>
                        <p class="text-gray-500 text-sm mb-4 line-clamp-2">{{ $event->description }}</p>
                        <div class="mt-auto border-t pt-4 flex items-center justify-between">
                            <span class="text-gray-600 text-sm"><span class="font-bold border-b border-gray-300 border-dashed pb-0.5">{{ $event->location_name }}</span></span>
                            <span class="text-orange-600 font-bold text-lg">
                                {{ $event->tickets->min('price') > 0 ? 'Rp ' . number_format($event->tickets->min('price'), 0, ',', '.') : 'FREE' }}
                            </span>
                        </div>
                    </div>
                </a>
                @empty
                <div class="col-span-full py-20 text-center">
                    <div class="inline-flex items-center justify-center w-24 h-24 rounded-full bg-gray-100 mb-4 text-gray-400">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-2">No active events right now!</h3>
                    <p class="text-gray-500 text-lg">Administrators and Organizers are currently setting up new exciting events. Check back later.</p>
                </div>
                @endforelse
            </div>
        </main>

        <footer class="bg-gray-900 text-gray-400 py-12 text-center">
            <div class="max-w-7xl mx-auto px-4"><p class="text-lg">Evoria © 2026. Made as Final Semester Project ABP.</p></div>
        </footer>

        <!-- AI Chatbot Floating Action Button -->
        <button @click="chatbotOpen = !chatbotOpen" class="fixed bottom-6 right-6 w-16 h-16 bg-gradient-to-tr from-blue-600 to-indigo-600 rounded-full text-white shadow-2xl flex items-center justify-center hover:scale-105 transition transform z-50">
            <svg x-show="!chatbotOpen" class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
            <svg x-cloak x-show="chatbotOpen" class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>

        <!-- AI Chatbot Popover Panel -->
        <div x-cloak x-show="chatbotOpen" x-transition.opacity.duration.300ms class="fixed bottom-28 right-6 w-96 h-[500px] bg-white rounded-2xl shadow-2xl border border-gray-100 flex flex-col z-50 overflow-hidden transform origin-bottom-right" x-data="chatBox()">
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 p-4 text-white flex items-center shadow-md z-10">
                <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center mr-3">
                    <span class="text-xl">🤖</span>
                </div>
                <div>
                    <h4 class="font-bold text-lg leading-tight">AI Assistant</h4>
                    <p class="text-blue-100 text-xs">I can recommend events based on rules.</p>
                </div>
            </div>
            
            <div class="flex-1 bg-gray-50 p-4 overflow-y-auto space-y-4" id="chat-messages">
                <div class="flex">
                    <div class="bg-gray-200 text-gray-800 rounded-2xl px-4 py-3 max-w-[85%] rounded-tl-none shadow-sm text-sm">
                        Hello! Looking for something specific? Ask me about concerts under Rp 100K this weekend!
                    </div>
                </div>
                
                <template x-for="message in messages">
                    <div :class="message.role === 'user' ? 'flex justify-end' : 'flex'">
                        <div :class="message.role === 'user' ? 'bg-blue-600 text-white rounded-tr-none' : 'bg-gray-200 text-gray-800 rounded-tl-none prose prose-sm max-w-none'" 
                             class="rounded-2xl px-4 py-3 max-w-[85%] shadow-sm text-sm" x-html="formatMessage(message.content)">
                        </div>
                    </div>
                </template>

                <div class="flex" x-show="loading">
                    <div class="bg-gray-200 text-gray-800 rounded-2xl px-4 py-3 max-w-[85%] rounded-tl-none shadow-sm">
                        <div class="flex space-x-1 mb-1 items-center h-4">
                            <div class="w-1.5 h-1.5 bg-gray-500 rounded-full animate-bounce"></div>
                            <div class="w-1.5 h-1.5 bg-gray-500 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                            <div class="w-1.5 h-1.5 bg-gray-500 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-4 border-t border-gray-200 bg-white">
                <form @submit.prevent="sendMessage" class="flex items-center space-x-2">
                    <input type="text" x-model="newMessage" placeholder="Type your prompt..." class="flex-1 bg-gray-100 border-none rounded-full px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition text-sm">
                    <button type="submit" :disabled="loading" class="w-12 h-12 bg-blue-600 text-white rounded-full flex items-center justify-center hover:bg-blue-700 transition disabled:opacity-50">
                        <svg class="w-5 h-5 ml-1" fill="currentColor" viewBox="0 0 20 20"><path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"></path></svg>
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
                            this.messages.push({ role: 'assistant', content: "Network error occurred." });
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
                        // Escape HTML first to prevent XSS from user input, but we allow safe tags later
                        let html = text.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
                        
                        // Parse bold **text**
                        html = html.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
                        
                        // Parse Markdown Links [Text](URL)
                        html = html.replace(/\[([^\]]+)\]\(([^)]+)\)/g, '<a href="$2" class="text-blue-600 hover:text-blue-800 underline disabled">$1</a>');
                        
                        // Parse new lines
                        html = html.replace(/\n/g, '<br>');
                        
                        return html;
                    }
                }));
            });
        </script>
    </body>
</html>
