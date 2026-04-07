<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if ($user->role === 'attendee')
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg border border-indigo-100">
                    <div class="max-w-2xl space-y-3">
                        <h3 class="text-lg font-semibold text-gray-900">Ingin jadi Event Organizer?</h3>
                        <p class="text-sm text-gray-600">
                            Ajukan akun organizer untuk mulai membuat event Anda sendiri. Pengajuan akan diverifikasi oleh admin.
                        </p>
                        <a href="{{ route('organizer.application.create') }}" class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-indigo-700">
                            Ajukan sebagai Event Organizer
                        </a>
                    </div>
                </div>
            @endif

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
