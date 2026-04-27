@if($user->role === 'attendee')
    <!DOCTYPE html>
    <html class="light" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Profile - {{ config('app.name', 'Evoria') }}</title>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
        <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            body { font-family: 'Plus Jakarta Sans', sans-serif; }
        </style>
    </head>
    <body class="bg-[#ebebeb] min-h-screen text-slate-900">
        <x-attendee-main-header />

        @php
            $avatarUrl = $user->profile_photo_path
                ? \Illuminate\Support\Facades\Storage::url($user->profile_photo_path)
                : "https://ui-avatars.com/api/?name=" . urlencode($user->name ?? 'User') . "&background=2563EB&color=ffffff&size=256";
        @endphp

        <main class="max-w-[1200px] mx-auto px-6 py-10 space-y-7">
            <section class="bg-white rounded-2xl border border-slate-100 p-6 md:p-8 shadow-sm">
                <p class="text-[12px] font-extrabold tracking-widest text-primary uppercase">Attendee Area</p>
                <h1 class="text-3xl md:text-4xl font-black text-slate-900 mt-2">Profile Saya</h1>
                <p class="text-slate-500 mt-2">Lihat dan perbarui informasi akun Anda.</p>
            </section>

            @if (session('status') === 'profile-updated')
                <div class="bg-green-100 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm font-semibold">
                    Profile berhasil diperbarui.
                </div>
            @endif

            <section class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm">
                    <img src="{{ $avatarUrl }}" alt="Foto profil {{ $user->name }}" class="w-28 h-28 rounded-full object-cover border-2 border-slate-100 mx-auto">
                    <h2 class="text-center text-lg font-bold text-slate-900 mt-4">{{ $user->name }}</h2>
                    <p class="text-center text-sm text-slate-500">{{ $user->email }}</p>
                    <div class="mt-5 pt-4 border-t border-slate-100 space-y-2 text-sm">
                        <p><span class="text-slate-500">Role:</span> <span class="font-semibold text-slate-900 capitalize">{{ $user->role }}</span></p>
                        <p><span class="text-slate-500">Status:</span> <span class="font-semibold text-slate-900 capitalize">{{ $user->status }}</span></p>
                        <p><span class="text-slate-500">Bergabung:</span> <span class="font-semibold text-slate-900">{{ $user->created_at->translatedFormat('d M Y') }}</span></p>
                    </div>
                </div>

                <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-100 p-6 shadow-sm">
                    <h3 class="text-xl font-bold text-slate-900">Edit Informasi Profil</h3>
                    <p class="text-sm text-slate-500 mt-1">Anda dapat mengganti nama, email, dan foto profil.</p>

                    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="mt-6 space-y-5">
                        @csrf
                        @method('patch')

                        <div>
                            <x-input-label for="name" :value="__('Name')" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
                            <x-input-error class="mt-2" :messages="$errors->get('name')" />
                        </div>

                        <div>
                            <x-input-label for="email" :value="__('Email')" />
                            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
                            <x-input-error class="mt-2" :messages="$errors->get('email')" />
                        </div>

                        <div>
                            <x-input-label for="profile_photo" :value="__('Photo Profile')" />
                            <input id="profile_photo" name="profile_photo" type="file" accept=".jpg,.jpeg,.png,.webp" class="mt-1 block w-full rounded-lg border-slate-300 text-sm">
                            <x-input-error class="mt-2" :messages="$errors->get('profile_photo')" />
                        </div>

                        @if($user->profile_photo_path)
                            <label class="inline-flex items-center gap-2 text-sm text-slate-600">
                                <input type="checkbox" name="remove_profile_photo" value="1" class="rounded border-slate-300">
                                Hapus foto profil saat ini
                            </label>
                        @endif

                        <div class="flex items-center gap-4 pt-2">
                            <x-primary-button>{{ __('Save') }}</x-primary-button>
                        </div>
                    </form>
                </div>
            </section>

            <section class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="p-6 bg-white shadow-sm rounded-2xl border border-slate-100">
                    @include('profile.partials.update-password-form')
                </div>
                <div class="p-6 bg-white shadow-sm rounded-2xl border border-slate-100">
                    @include('profile.partials.delete-user-form')
                </div>
            </section>
        </main>
    </body>
    </html>
@else
    <x-app-layout>
        <x-slot name="header">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Profile') }}
            </h2>
        </x-slot>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <div class="max-w-xl">
                        @include('profile.partials.update-profile-information-form')
                    </div>
                </div>

                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <div class="max-w-xl">
                        @include('profile.partials.update-password-form')
                    </div>
                </div>

                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <div class="max-w-xl">
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>
            </div>
        </div>
    </x-app-layout>
@endif
