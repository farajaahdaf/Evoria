<x-app-layout>
    <div class="min-h-screen bg-[#ebebeb]">
        <div class="mx-auto flex w-full max-w-7xl flex-1 items-stretch px-5 py-8 sm:px-8 lg:px-10 lg:py-10" x-data="eventForm()">
            <div class="grid w-full flex-1 overflow-hidden rounded-[32px] border border-slate-200/80 bg-white shadow-[0_20px_80px_rgba(15,23,42,0.08)] lg:grid-cols-[minmax(0,1fr)_minmax(420px,0.8fr)]">
                <!-- Left Section: Form -->
                <section class="flex flex-col bg-white p-8 lg:p-12 overflow-y-auto">
                    <div class="mb-8 flex items-center justify-between">
                        <div>
                            <h2 class="text-3xl font-black text-slate-900">Buat Event Baru</h2>
                            <p class="mt-2 text-slate-500">Lengkapi detail event Anda dalam dua langkah mudah.</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <div :class="step === 1 ? 'bg-primary text-white scale-110' : 'bg-slate-100 text-slate-400'" class="flex h-10 w-10 items-center justify-center rounded-2xl font-bold transition-all duration-300">1</div>
                            <div class="h-1 w-8 rounded-full bg-slate-100">
                                <div :class="step === 2 ? 'w-full bg-primary' : 'w-0 bg-primary'" class="h-full transition-all duration-500"></div>
                            </div>
                            <div :class="step === 2 ? 'bg-primary text-white scale-110' : 'bg-slate-100 text-slate-400'" class="flex h-10 w-10 items-center justify-center rounded-2xl font-bold transition-all duration-300">2</div>
                        </div>
                    </div>

                    @if ($errors->any())
                    <div class="mb-8 rounded-2xl border border-red-100 bg-red-50 p-4">
                        <ul class="list-inside list-disc text-sm font-medium text-red-700">
                            @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                        </ul>
                    </div>
                    @endif

                    <form method="POST" action="{{ route('organizer.events.store') }}" class="space-y-10" enctype="multipart/form-data" id="event-form">
                        @csrf
                        
                        <!-- Step 1: Basic & Ticketing -->
                        <div x-show="step === 1" class="space-y-10" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-x-4" x-transition:enter-end="opacity-100 transform translate-x-0">
                            <!-- Basic Information -->
                            <div class="space-y-6">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-50 text-sm font-bold text-primary">01</span>
                                    <h3 class="text-xl font-bold text-slate-900">Informasi Dasar</h3>
                                </div>
                                
                                <div class="space-y-4">
                                    <div class="space-y-2">
                                        <label for="title" class="text-sm font-semibold text-slate-700">Judul Event</label>
                                        <input id="title" name="title" type="text" value="{{ old('title') }}" required autofocus placeholder="Contoh: Konser Musik Harmoni Bangsa" class="block w-full rounded-2xl border border-slate-200 bg-white px-4 py-3.5 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-primary focus:ring-4 focus:ring-blue-100">
                                    </div>

                                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                        <div class="space-y-2">
                                            <label for="category_id" class="text-sm font-semibold text-slate-700">Kategori</label>
                                            <select id="category_id" name="category_id" required class="block w-full rounded-2xl border border-slate-200 bg-white px-4 py-3.5 text-sm text-slate-900 outline-none transition focus:border-primary focus:ring-4 focus:ring-blue-100">
                                                <option value="">Pilih Kategori</option>
                                                @foreach($categories as $category)
                                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="space-y-2">
                                            <label for="banner" class="text-sm font-semibold text-slate-700">Banner Event</label>
                                            <input id="banner" name="banner" type="file" accept="image/*" class="block w-full text-xs text-slate-500 file:mr-4 file:rounded-full file:border-0 file:bg-blue-50 file:px-4 file:py-2.5 file:text-xs file:font-semibold file:text-primary hover:file:bg-blue-100">
                                        </div>
                                    </div>

                                    <div class="space-y-2">
                                        <label for="description" class="text-sm font-semibold text-slate-700">Deskripsi Lengkap</label>
                                        <textarea id="description" name="description" rows="5" required placeholder="Jelaskan detail event Anda..." class="block w-full rounded-2xl border border-slate-200 bg-white px-4 py-3.5 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-primary focus:ring-4 focus:ring-blue-100">{{ old('description') }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Schedule & Location -->
                            <div class="space-y-6">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-50 text-sm font-bold text-primary">02</span>
                                    <h3 class="text-xl font-bold text-slate-900">Waktu & Lokasi</h3>
                                </div>

                                <div class="space-y-4">
                                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                        <div class="space-y-2">
                                            <label for="start_time" class="text-sm font-semibold text-slate-700">Waktu Mulai</label>
                                            <input id="start_time" name="start_time" type="datetime-local" value="{{ old('start_time') }}" required class="block w-full rounded-2xl border border-slate-200 bg-white px-4 py-3.5 text-sm text-slate-900 outline-none transition focus:border-primary focus:ring-4 focus:ring-blue-100">
                                        </div>
                                        <div class="space-y-2">
                                            <label for="end_time" class="text-sm font-semibold text-slate-700">Waktu Selesai</label>
                                            <input id="end_time" name="end_time" type="datetime-local" value="{{ old('end_time') }}" required class="block w-full rounded-2xl border border-slate-200 bg-white px-4 py-3.5 text-sm text-slate-900 outline-none transition focus:border-primary focus:ring-4 focus:ring-blue-100">
                                        </div>
                                    </div>

                                    <div class="space-y-2">
                                        <label for="location_name" class="text-sm font-semibold text-slate-700">Nama Tempat / Venue</label>
                                        <input id="location_name" name="location_name" type="text" value="{{ old('location_name') }}" required placeholder="Contoh: Jakarta Convention Center" class="block w-full rounded-2xl border border-slate-200 bg-white px-4 py-3.5 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-primary focus:ring-4 focus:ring-blue-100">
                                        <p class="text-xs text-slate-500">Mulai ketik nama gedung, venue, atau tempat. Pilih saran Google Maps agar alamat dan koordinat terisi otomatis.</p>
                                    </div>
                                    
                                    <div class="space-y-2">
                                        <label for="address" class="text-sm font-semibold text-slate-700">Alamat Lengkap</label>
                                        <textarea id="address" name="address" rows="2" class="block w-full rounded-2xl border border-slate-200 bg-white px-4 py-3.5 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-primary focus:ring-4 focus:ring-blue-100">{{ old('address') }}</textarea>
                                    </div>

                                    <div class="space-y-4">
                                        <div class="flex items-center justify-between gap-4">
                                            <div>
                                                <label class="text-sm font-semibold text-slate-700">Lokasi di Peta</label>
                                                <p class="mt-1 text-xs text-slate-500">Cari venue lewat Google Maps atau klik langsung pada peta untuk menentukan titik event.</p>
                                            </div>
                                            <button
                                                type="button"
                                                @click="resetMapPosition()"
                                                class="rounded-full border border-slate-200 px-4 py-2 text-xs font-bold text-slate-600 transition hover:border-primary hover:text-primary"
                                            >
                                                Reset Peta
                                            </button>
                                        </div>

                                        <div id="event-location-map" class="h-80 w-full rounded-3xl border border-slate-200 bg-slate-100"></div>

                                        <input id="latitude" name="latitude" type="hidden" x-model="latitude">
                                        <input id="longitude" name="longitude" type="hidden" x-model="longitude">

                                        <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-xs text-slate-600">
                                            <span class="font-bold text-slate-700">Koordinat tersimpan otomatis:</span>
                                            <span x-text="latitude || '-'"></span>,
                                            <span x-text="longitude || '-'"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Ticketing -->
                            <div class="space-y-6">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-50 text-sm font-bold text-primary">03</span>
                                        <h3 class="text-xl font-bold text-slate-900">Konfigurasi Tiket</h3>
                                    </div>
                                    <button type="button" @click="addTicket" class="text-sm font-bold text-primary hover:underline">+ Tambah Tier</button>
                                </div>
                                
                                <div class="space-y-4">
                                    <template x-for="(ticket, index) in tickets" :key="ticket.id">
                                        <div class="group relative rounded-2xl border border-slate-100 bg-slate-50/50 p-6 transition hover:border-primary/30">
                                            <button type="button" @click="removeTicket(index)" x-show="tickets.length > 1" class="absolute -right-2 -top-2 flex h-8 w-8 items-center justify-center rounded-full bg-white text-red-500 shadow-sm border border-slate-100 transition hover:bg-red-50">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                            </button>
                                            
                                            <div class="grid grid-cols-1 gap-6 md:grid-cols-12">
                                                <div class="md:col-span-6 space-y-2">
                                                    <label class="text-xs font-bold uppercase tracking-wider text-slate-500">Nama Tiket</label>
                                                    <input type="text" x-model="ticket.name" :name="'tickets['+index+'][name]'" placeholder="VIP, Regular, dsb." required class="block w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-primary">
                                                </div>
                                                <div class="md:col-span-3 space-y-2">
                                                    <label class="text-xs font-bold uppercase tracking-wider text-slate-500">Harga (Rp)</label>
                                                    <input type="number" x-model="ticket.price" :name="'tickets['+index+'][price]'" placeholder="0" required min="0" class="block w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-primary">
                                                </div>
                                                <div class="md:col-span-3 space-y-2">
                                                    <label class="text-xs font-bold uppercase tracking-wider text-slate-500">Kuota</label>
                                                    <input type="number" x-model="ticket.quota" :name="'tickets['+index+'][quota]'" placeholder="100" required min="1" class="block w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-primary">
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <div class="flex items-center justify-between pt-6 border-t border-slate-100">
                                <a href="{{ route('organizer.events.index') }}" class="text-sm font-bold text-slate-400 transition hover:text-slate-600">Batal</a>
                                <button type="button" @click="nextStep()" class="inline-flex items-center gap-2 rounded-2xl bg-primary px-8 py-4 text-sm font-extrabold text-white shadow-lg shadow-blue-200 transition hover:-translate-y-0.5 hover:bg-primary/90">
                                    Langkah Selanjutnya
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                </button>
                            </div>
                        </div>

                        <!-- Step 2: Documents -->
                        <div x-show="step === 2" class="space-y-10" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-x-4" x-transition:enter-end="opacity-100 transform translate-x-0">
                            <div class="space-y-6">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-50 text-sm font-bold text-primary">04</span>
                                    <h3 class="text-xl font-bold text-slate-900">Dokumen Pendukung</h3>
                                </div>
                                <p class="text-sm text-slate-500">Unggah dokumen tambahan untuk membantu kami memverifikasi event Anda.</p>
                                
                                <div class="grid grid-cols-1 gap-8 md:grid-cols-2">
                                    <!-- Portfolio -->
                                    <div class="rounded-3xl border-2 border-dashed border-slate-200 bg-slate-50/50 p-8 text-center transition hover:border-primary/50">
                                        <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-blue-100 text-primary">
                                            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        </div>
                                        <label for="portfolio" class="block text-base font-bold text-slate-900">File Portofolio</label>
                                        <p class="mt-1 text-xs text-slate-500">PDF atau Gambar (Maks. 5MB).</p>
                                        <input id="portfolio" name="portfolio" type="file" accept=".pdf,image/*" class="mt-6 block w-full text-xs text-slate-500 file:mr-4 file:rounded-full file:border-0 file:bg-primary file:px-4 file:py-2 file:text-xs file:font-bold file:text-white hover:file:bg-primary/90">
                                    </div>

                                    <!-- Proposal -->
                                    <div class="rounded-3xl border-2 border-dashed border-slate-200 bg-slate-50/50 p-8 text-center transition hover:border-primary/50">
                                        <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-600">
                                            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        </div>
                                        <label for="proposal" class="block text-base font-bold text-slate-900">Proposal Event</label>
                                        <p class="mt-1 text-xs text-slate-500">Hanya file PDF (Maks. 10MB).</p>
                                        <input id="proposal" name="proposal" type="file" accept=".pdf" class="mt-6 block w-full text-xs text-slate-500 file:mr-4 file:rounded-full file:border-0 file:bg-emerald-600 file:px-4 file:py-2 file:text-xs file:font-bold file:text-white hover:file:bg-emerald-700">
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center justify-between pt-6 border-t border-slate-100">
                                <button type="button" @click="step = 1" class="text-sm font-bold text-slate-400 transition hover:text-slate-600 flex items-center gap-2">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                                    Kembali ke Detail
                                </button>
                                <div class="flex items-center gap-3">
                                    <button type="submit" name="action" value="draft" class="inline-flex items-center gap-2 rounded-2xl bg-slate-100 px-8 py-4 text-sm font-extrabold text-slate-700 transition hover:-translate-y-0.5 hover:bg-slate-200">
                                        Simpan Draft
                                    </button>
                                    <button type="submit" name="action" value="submit" class="inline-flex items-center gap-2 rounded-2xl bg-primary px-10 py-4 text-sm font-extrabold text-white shadow-xl shadow-blue-200 transition hover:-translate-y-0.5 hover:bg-primary/90">
                                        Ajukan Event Sekarang
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </section>

                <!-- Right Section: Preview / Sidebar -->
                <section class="relative hidden bg-slate-900 p-12 text-white lg:block">
                    <div class="absolute inset-0 opacity-20">
                        <img src="https://images.unsplash.com/photo-1501281668745-f7f57925c3b4?auto=format&fit=crop&w=1200&q=80" class="h-full w-full object-cover">
                    </div>
                    <div class="relative z-10 flex h-full flex-col justify-between">
                        <div class="space-y-8">
                            <div class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-4 py-2 text-[10px] font-bold uppercase tracking-widest text-white/80">
                                Organizer Hub
                            </div>
                            
                            <div class="space-y-4">
                                <h4 class="text-4xl font-black leading-tight">Mulai perjalanan event sukses Anda di sini.</h4>
                                <p class="text-slate-400">Pastikan semua data yang diisi benar agar proses verifikasi oleh admin berjalan lancar.</p>
                            </div>

                            <div class="space-y-6 pt-12">
                                <div class="flex items-start gap-4">
                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-primary shadow-lg shadow-blue-500/20">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    </div>
                                    <div>
                                        <h5 class="font-bold">Verifikasi Cepat</h5>
                                        <p class="text-sm text-slate-400">Admin kami meninjau setiap proposal dalam waktu kurang dari 24 jam.</p>
                                    </div>
                                </div>
                                <div class="flex items-start gap-4">
                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-800 border border-white/10">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                    </div>
                                    <div>
                                        <h5 class="font-bold">Aman & Terpercaya</h5>
                                        <p class="text-sm text-slate-400">Seluruh data event dan pembayaran Anda dilindungi sistem enkripsi terbaru.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="rounded-3xl border border-white/10 bg-white/5 p-6 backdrop-blur-md">
                            <p class="text-xs font-bold uppercase tracking-widest text-slate-500">Status Akun</p>
                            <div class="mt-3 flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></div>
                                    <span class="text-sm font-bold">Terverifikasi</span>
                                </div>
                                <span class="text-xs text-slate-400">Ready to publish</span>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('eventForm', () => ({
                step: 1,
                latitude: '{{ old('latitude', config('services.google_maps.default_lat')) }}',
                longitude: '{{ old('longitude', config('services.google_maps.default_lng')) }}',
                defaultLat: {{ (float) config('services.google_maps.default_lat') }},
                defaultLng: {{ (float) config('services.google_maps.default_lng') }},
                defaultZoom: {{ (int) config('services.google_maps.default_zoom') }},
                map: null,
                marker: null,
                autocomplete: null,
                tickets: [
                    { id: Date.now(), name: '', price: '', quota: '' }
                ],
                init() {
                    this.waitForGoogleMaps();
                },
                waitForGoogleMaps() {
                    if (window.google && window.google.maps) {
                        this.$nextTick(() => {
                            this.initMap();
                            this.initAutocomplete();
                        });
                        return;
                    }

                    window.setTimeout(() => this.waitForGoogleMaps(), 150);
                },
                initMap() {
                    const lat = parseFloat(this.latitude) || this.defaultLat;
                    const lng = parseFloat(this.longitude) || this.defaultLng;
                    const center = { lat, lng };

                    this.map = new google.maps.Map(document.getElementById('event-location-map'), {
                        center,
                        zoom: this.defaultZoom,
                        mapTypeControl: false,
                        streetViewControl: false,
                        fullscreenControl: false,
                    });

                    this.marker = new google.maps.Marker({
                        position: center,
                        map: this.map,
                        draggable: true,
                    });

                    this.updateCoordinates(lat, lng);

                    this.map.addListener('click', (event) => {
                        this.setMarkerPosition(event.latLng);
                    });

                    this.marker.addListener('dragend', (event) => {
                        this.setMarkerPosition(event.latLng);
                    });
                },
                initAutocomplete() {
                    const input = document.getElementById('location_name');

                    if (!input || !google.maps.places) {
                        return;
                    }

                    this.autocomplete = new google.maps.places.Autocomplete(input, {
                        fields: ['geometry', 'formatted_address', 'name'],
                    });

                    this.autocomplete.addListener('place_changed', () => {
                        const place = this.autocomplete.getPlace();

                        if (!place.geometry || !place.geometry.location) {
                            return;
                        }

                        document.getElementById('location_name').value = place.name || document.getElementById('location_name').value;
                        document.getElementById('address').value = place.formatted_address || document.getElementById('address').value;

                        this.setMarkerPosition(place.geometry.location, true);
                    });
                },
                setMarkerPosition(location, panMap = false) {
                    const lat = typeof location.lat === 'function' ? location.lat() : location.lat;
                    const lng = typeof location.lng === 'function' ? location.lng() : location.lng;

                    this.marker.setPosition({ lat, lng });
                    this.updateCoordinates(lat, lng);

                    if (panMap && this.map) {
                        this.map.panTo({ lat, lng });
                        this.map.setZoom(Math.max(this.map.getZoom(), 15));
                    }
                },
                updateCoordinates(lat, lng) {
                    this.latitude = Number(lat).toFixed(8);
                    this.longitude = Number(lng).toFixed(8);
                },
                resetMapPosition() {
                    if (!this.map || !this.marker) {
                        return;
                    }

                    this.setMarkerPosition({ lat: this.defaultLat, lng: this.defaultLng }, true);
                },
                addTicket() {
                    this.tickets.push({ id: Date.now(), name: '', price: '', quota: '' });
                },
                removeTicket(index) {
                    this.tickets.splice(index, 1);
                },
                nextStep() {
                    // Basic validation for step 1
                    const form = document.getElementById('event-form');
                    const requiredInputs = form.querySelectorAll('[x-show="step === 1"] [required]');
                    let allValid = true;
                    
                    requiredInputs.forEach(input => {
                        if (!input.checkValidity()) {
                            input.reportValidity();
                            allValid = false;
                        }
                    });

                    if (allValid) {
                        this.step = 2;
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    }
                }
            }))
        })
    </script>
    @if(config('services.google_maps.web_api_key'))
    <script
        async
        defer
        src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.web_api_key') }}&libraries=places"
    ></script>
    @endif
    <style>
        [x-cloak] { display: none !important; }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #cbd5e1; }
    </style>
    @endpush
</x-app-layout>
