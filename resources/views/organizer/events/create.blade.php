<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
            {{ __('Create New Event') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="eventForm()">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl">
                
                <div class="p-8 border-b border-gray-100">
                    <h3 class="text-xl font-bold text-gray-800">Event Details & Ticketing</h3>
                    <p class="text-gray-500 text-sm mt-1">Fill out the basic information and configure your ticket quotas.</p>
                </div>

                @if ($errors->any())
                <div class="bg-red-50 p-4 border-l-4 border-red-500 mx-8 mt-6">
                    <ul class="text-red-700 list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
                @endif

                <form method="POST" action="{{ route('organizer.events.store') }}" class="p-8 space-y-8" enctype="multipart/form-data">
                    @csrf
                    
                    <!-- Basic Info -->
                    <div>
                        <h4 class="text-lg font-semibold text-gray-800 border-b pb-2 mb-4">1. Basic Information</h4>
                        <div class="space-y-4">
                            <div>
                                <x-input-label for="title" value="{{ __('Event Title') }}" />
                                <x-text-input id="title" class="block w-full mt-1" type="text" name="title" :value="old('title')" required autofocus />
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <x-input-label for="category_id" value="{{ __('Category') }}" />
                                    <select id="category_id" name="category_id" class="block w-full mt-1 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                        <option value="">Select Category</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <x-input-label for="banner" value="{{ __('Event Banner (Image)') }}" />
                                    <input id="banner" class="block w-full mt-1 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm p-1.5" type="file" name="banner" accept="image/*" />
                                </div>
                            </div>

                            <div>
                                <x-input-label for="description" value="{{ __('Description (Markdown / HTML supported eventually)') }}" />
                                <textarea id="description" name="description" rows="4" class="block w-full mt-1 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>{{ old('description') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Schedule & Location -->
                    <div>
                        <h4 class="text-lg font-semibold text-gray-800 border-b pb-2 mb-4">2. Schedule & Location</h4>
                        <div class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <x-input-label for="start_time" value="{{ __('Start Time') }}" />
                                    <x-text-input id="start_time" class="block w-full mt-1" type="datetime-local" name="start_time" :value="old('start_time')" required />
                                </div>
                                <div>
                                    <x-input-label for="end_time" value="{{ __('End Time') }}" />
                                    <x-text-input id="end_time" class="block w-full mt-1" type="datetime-local" name="end_time" :value="old('end_time')" required />
                                </div>
                            </div>

                            <div>
                                <x-input-label for="location_name" value="{{ __('Venue Name') }}" />
                                <x-text-input id="location_name" class="block w-full mt-1" type="text" name="location_name" :value="old('location_name')" placeholder="e.g. Jakarta Convention Center" required />
                            </div>
                            
                            <div>
                                <x-input-label for="address" value="{{ __('Full Address') }}" />
                                <textarea id="address" name="address" rows="2" class="block w-full mt-1 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>{{ old('address') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Ticketing (AlpineJs Dynamic form) -->
                    <div>
                        <div class="flex items-center justify-between border-b pb-2 mb-4">
                            <h4 class="text-lg font-semibold text-gray-800">3. Ticket Configuration</h4>
                            <button type="button" @click="addTicket" class="text-sm font-bold text-indigo-600 hover:text-indigo-800">+ Add Ticket Tier</button>
                        </div>
                        
                        <div class="space-y-4">
                            <template x-for="(ticket, index) in tickets" :key="ticket.id">
                                <div class="p-4 bg-gray-50 border border-gray-200 rounded-lg relative flex flex-col md:flex-row gap-4 items-end">
                                    <button type="button" @click="removeTicket(index)" x-show="tickets.length > 1" class="absolute -top-2 -right-2 bg-red-100 text-red-600 rounded-full w-6 h-6 flex items-center justify-center font-bold hover:bg-red-200 shadow">&times;</button>
                                    
                                    <div class="flex-1 w-full">
                                        <label class="block font-medium text-sm text-gray-700">Ticket Name</label>
                                        <input type="text" x-model="ticket.name" :name="'tickets['+index+'][name]'" class="block w-full mt-1 border-gray-300 focus:border-indigo-500 rounded-md shadow-sm" placeholder="e.g. Regular, VIP" required>
                                    </div>
                                    <div class="w-full md:w-1/4">
                                        <label class="block font-medium text-sm text-gray-700">Price (IDR)</label>
                                        <input type="number" x-model="ticket.price" :name="'tickets['+index+'][price]'" class="block w-full mt-1 border-gray-300 focus:border-indigo-500 rounded-md shadow-sm" placeholder="0 for Free" required min="0">
                                    </div>
                                    <div class="w-full md:w-1/4">
                                        <label class="block font-medium text-sm text-gray-700">Quota Amount</label>
                                        <input type="number" x-model="ticket.quota" :name="'tickets['+index+'][quota]'" class="block w-full mt-1 border-gray-300 focus:border-indigo-500 rounded-md shadow-sm" placeholder="e.g. 100" required min="1">
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div class="pt-6 border-t flex justify-end space-x-3">
                        <a href="{{ route('organizer.events.index') }}" class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium transition">Cancel</a>
                        <button type="submit" class="px-6 py-3 bg-indigo-600 text-white font-bold rounded-lg shadow hover:bg-indigo-700 transition">Save as Draft</button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('eventForm', () => ({
                tickets: [
                    { id: Date.now(), name: '', price: '', quota: '' }
                ],
                addTicket() {
                    this.tickets.push({ id: Date.now(), name: '', price: '', quota: '' });
                },
                removeTicket(index) {
                    this.tickets.splice(index, 1);
                }
            }))
        })
    </script>
    @endpush
</x-app-layout>
