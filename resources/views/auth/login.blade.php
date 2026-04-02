<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8"/>
  <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
  <title>{{ config('app.name', 'Evoria') }} - Login</title>
  
  <!-- Tailwind CSS v3 with Plugins -->
  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
  
  <!-- Alpine JS -->
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
  
  <!-- Vite (optional, for other global styles/scripts) -->
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  
  <style data-purpose="custom-typography">
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
    body {
      font-family: 'Inter', sans-serif;
    }
  </style>
  <style data-purpose="custom-layout">
    /* Subtle background gradient to match the premium feel of the landing page */
    .bg-premium-mesh {
      background-color: #ffffff;
      background-image: 
        radial-gradient(at 0% 0%, rgba(37, 99, 235, 0.03) 0px, transparent 50%),
        radial-gradient(at 100% 100%, rgba(37, 99, 235, 0.03) 0px, transparent 50%);
    }
  </style>
</head>
<body class="bg-premium-mesh min-h-screen flex flex-col">
  <!-- BEGIN: Navigation Header -->
  <header class="w-full py-6 px-4 md:px-8 flex justify-between items-center bg-white/80 backdrop-blur-md border-b border-gray-100 sticky top-0 z-50">
    <a href="/" class="flex items-center gap-2">
      <!-- Logo placeholder based on Image_1 colors/style -->
      <div class="flex items-center gap-2">
        <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
          <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewbox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
          </svg>
        </div>
        <span class="text-2xl font-bold tracking-tight text-slate-900">Evoria</span>
      </div>
    </a>
    <nav class="hidden md:flex items-center gap-8 text-sm font-medium text-slate-600">
      <a class="hover:text-blue-600 transition-colors" href="/">Event</a>
    </nav>
    <div>
      <a class="text-sm font-semibold text-blue-600 hover:text-blue-700" href="/">Kembali ke Beranda</a>
    </div>
  </header>
  <!-- END: Navigation Header -->

  <!-- BEGIN: Main Login Section -->
  <main class="flex-grow flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-md" data-purpose="login-card-container">
      <!-- Heading Area -->
      <div class="text-center mb-10">
        <h1 class="text-3xl font-bold text-slate-900 mb-3">Selamat Datang Kembali</h1>
        <p class="text-slate-500">Masuk untuk mengakses tiket eksklusif dan pengalaman VIP Anda.</p>
      </div>
      
      <!-- Session Status -->
      @if (session('status'))
        <div class="mb-4 font-medium text-sm text-green-600">
            {{ session('status') }}
        </div>
      @endif

      <!-- Login Form Card -->
      <div class="bg-white rounded-3xl shadow-xl shadow-blue-500/5 border border-slate-100 p-8 md:p-10">
        <form action="{{ route('login') }}" class="space-y-6" method="POST">
          @csrf
          
          <!-- Email Field -->
          <div class="space-y-2">
            <label class="block text-sm font-semibold text-slate-700" for="email">Email</label>
            <input class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all outline-none" id="email" name="email" value="{{ old('email') }}" placeholder="nama@email.com" required autofocus autocomplete="username" type="email"/>
            @error('email')
                <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
            @enderror
          </div>
          
          <!-- Password Field -->
          <div class="space-y-2">
            <div class="flex justify-between items-center">
              <label class="block text-sm font-semibold text-slate-700" for="password">Kata Sandi</label>
              @if (Route::has('password.request'))
                <a class="text-xs font-medium text-blue-600 hover:underline" href="{{ route('password.request') }}">Lupa Kata Sandi?</a>
              @endif
            </div>
            <div class="relative" x-data="{ show: false }">
              <input class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all outline-none pr-12" id="password" name="password" placeholder="••••••••" required autocomplete="current-password" :type="show ? 'text' : 'password'"/>
              <button @click="show = !show" class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600" type="button">
                <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewbox="0 0 24 24"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                <svg x-cloak x-show="show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path></svg>
              </button>
            </div>
            @error('password')
                <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
            @enderror
          </div>
          
          <!-- Remember Me -->
          <div class="flex items-center">
            <input class="w-4 h-4 text-blue-600 border-slate-300 rounded focus:ring-blue-500" name="remember" id="remember" type="checkbox"/>
            <label class="ml-2 text-sm text-slate-600" for="remember">Ingat saya di perangkat ini</label>
          </div>
          
          <!-- Submit Button -->
          <button class="w-full py-4 bg-[#2563EB] hover:bg-blue-700 text-white font-bold rounded-xl transition-all shadow-lg shadow-blue-500/25 flex items-center justify-center gap-2 group" type="submit">
            Masuk Sekarang
            <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewbox="0 0 24 24"><path d="M13 7l5 5m0 0l-5 5m5-5H6" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path></svg>
          </button>
        </form>

      </div>
      
      <!-- Registration Link -->
      <p class="text-center mt-8 text-slate-600">
        Belum punya akun? 
        <a class="text-blue-600 font-bold hover:underline" href="{{ route('register') }}">Daftar Akun Evoria</a>
      </p>
    </div>
  </main>
  <!-- END: Main Login Section -->

  <!-- BEGIN: Footer -->
  <footer class="w-full py-10 px-4 md:px-8 border-t border-slate-100 mt-auto bg-white">
    <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center gap-6">
      <div class="flex items-center gap-2">
        <div class="w-6 h-6 bg-blue-600 rounded flex items-center justify-center">
          <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewbox="0 0 24 24"><path d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path></svg>
        </div>
        <span class="text-lg font-bold text-slate-900">Evoria</span>
      </div>
      <p class="text-xs text-slate-400">© 2026 Evoria Premium Ticketing. Hak cipta dilindungi undang-undang.</p>
    </div>
  </footer>
  <!-- END: Footer -->
</body>
</html>
