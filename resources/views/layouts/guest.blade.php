<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Evoria') }}</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

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
    </head>
    <body class="min-h-screen bg-slate-100 text-slate-900 antialiased" style="font-family: 'Plus Jakarta Sans', sans-serif;">
        <div class="relative min-h-screen overflow-hidden">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,_rgba(37,99,235,0.1),_transparent_34%),radial-gradient(circle_at_bottom_right,_rgba(14,116,144,0.09),_transparent_34%),linear-gradient(180deg,_#f8fafc_0%,_#f1f5f9_100%)]"></div>
            <div class="absolute inset-y-0 right-0 hidden w-[46%] bg-[linear-gradient(180deg,_rgba(15,23,42,0.95)_0%,_rgba(30,41,59,0.92)_100%)] lg:block"></div>

            <div class="relative z-10 flex min-h-screen flex-col">
                <header class="px-5 py-5 sm:px-8 lg:px-10">
                    <div class="mx-auto flex w-full max-w-7xl items-center justify-between">
                        <a href="/" class="flex items-center">
                            <x-application-logo class="h-10 w-auto" />
                        </a>

                        <a href="/" class="inline-flex items-center gap-2 rounded-full border border-slate-200/80 bg-white/80 px-4 py-2 text-sm font-semibold text-slate-700 backdrop-blur transition hover:border-blue-200 hover:text-blue-600">
                            Kembali ke beranda
                        </a>
                    </div>
                </header>

                <main class="mx-auto flex w-full max-w-7xl flex-1 items-stretch px-5 pb-8 sm:px-8 lg:px-10 lg:pb-10">
                    <div class="grid w-full flex-1 overflow-hidden rounded-[32px] border border-slate-200/80 bg-white shadow-[0_20px_80px_rgba(15,23,42,0.08)] lg:grid-cols-[minmax(0,1.05fr)_minmax(380px,0.95fr)]">
                        <section class="relative hidden min-h-[720px] overflow-hidden lg:block">
                            <div class="absolute inset-0">
                                <img src="https://images.unsplash.com/photo-1492684223066-81342ee5ff30?auto=format&fit=crop&w=1600&q=80" alt="Audience enjoying a live event" class="h-full w-full object-cover">
                                <div class="absolute inset-0 bg-[linear-gradient(135deg,_rgba(15,23,42,0.72)_0%,_rgba(15,23,42,0.38)_46%,_rgba(37,99,235,0.18)_100%)]"></div>
                            </div>

                            <div class="relative flex h-full flex-col justify-between p-10 text-white xl:p-12">
                                <div class="inline-flex w-fit items-center gap-2 rounded-full border border-white/20 bg-white/10 px-4 py-2 text-xs font-bold uppercase tracking-[0.24em] text-white/90 backdrop-blur">
                                    Evoria Access
                                </div>

                                <div class="max-w-xl space-y-6">
                                    <div class="space-y-4">
                                        <p class="text-sm font-semibold uppercase tracking-[0.24em] text-blue-100/80">Offline events marketplace</p>
                                        <h1 class="text-4xl font-black leading-tight xl:text-5xl">Masuk ke ekosistem event yang terasa premium dari awal sampai checkout.</h1>
                                        <p class="max-w-lg text-base leading-7 text-slate-200">
                                            Jelajahi event unggulan, kelola publikasi, dan simpan tiket Anda dalam satu pengalaman yang konsisten.
                                        </p>
                                    </div>

                                    <div class="grid max-w-md grid-cols-3 gap-4 border-t border-white/15 pt-6 text-sm">
                                        <div>
                                            <p class="text-2xl font-extrabold">3</p>
                                            <p class="mt-1 text-white/72">Role terintegrasi</p>
                                        </div>
                                        <div>
                                            <p class="text-2xl font-extrabold">1</p>
                                            <p class="mt-1 text-white/72">Dashboard terpadu</p>
                                        </div>
                                        <div>
                                            <p class="text-2xl font-extrabold">24/7</p>
                                            <p class="mt-1 text-white/72">Akses akun</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <section class="flex items-center bg-white px-5 py-8 sm:px-8 lg:px-10 xl:px-12">
                            <div class="mx-auto w-full max-w-md animate-[fadeIn_.45s_ease-out]">
                                {{ $slot }}
                            </div>
                        </section>
                    </div>
                </main>
            </div>
        </div>
    </body>
</html>
