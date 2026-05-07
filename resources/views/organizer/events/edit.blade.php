<x-app-layout>
    <div class="min-h-screen bg-[#ebebeb] py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto">

            <div class="mb-6 flex items-center gap-3">
                <a href="{{ route('organizer.events.index') }}" class="text-sm font-semibold text-slate-500 hover:text-primary transition-colors flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    Event Saya
                </a>
                <span class="text-slate-300">/</span>
                <span class="text-sm font-semibold text-slate-900 truncate max-w-xs">{{ $event->title }}</span>
            </div>

            <div class="bg-white rounded-[32px] border border-slate-200/80 shadow-[0_20px_80px_rgba(15,23,42,0.08)] p-8 lg:p-12">
                <div class="mb-8">
                    <h2 class="text-3xl font-black text-slate-900">Edit Event</h2>
                    <p class="mt-2 text-slate-500">Perbarui detail event Anda. Status akan kembali ke draft setelah disimpan.</p>
                </div>

                @if(session('error'))
                    <div class="mb-6 rounded-2xl border border-red-100 bg-red-50 p-4 text-sm font-medium text-red-700">
                        {{ session('error') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-6 rounded-2xl border border-red-100 bg-red-50 p-4">
                        <ul class="list-inside list-disc text-sm font-medium text-red-700">
                            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('organizer.events.update', $event->id) }}" enctype="multipart/form-data" class="space-y-10">
                    @csrf
                    @method('PUT')

                    <!-- Basic Information -->
                    <div class="space-y-6">
                        <div class="flex items-center gap-3">
                            <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-50 text-sm font-bold text-primary">01</span>
                            <h3 class="text-xl font-bold text-slate-900">Informasi Dasar</h3>
                        </div>

                        <div class="space-y-4">
                            <div class="space-y-2">
                                <label for="title" class="text-sm font-semibold text-slate-700">Judul Event</label>
                                <input id="title" name="title" type="text" value="{{ old('title', $event->title) }}" required
                                    class="block w-full rounded-2xl border border-slate-200 bg-white px-4 py-3.5 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-primary focus:ring-4 focus:ring-blue-100">
                            </div>

                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <div class="space-y-2">
                                    <label for="category_id" class="text-sm font-semibold text-slate-700">Kategori</label>
                                    <select id="category_id" name="category_id" required
                                        class="block w-full rounded-2xl border border-slate-200 bg-white px-4 py-3.5 text-sm text-slate-900 outline-none transition focus:border-primary focus:ring-4 focus:ring-blue-100">
                                        <option value="">Pilih Kategori</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('category_id', $event->category_id) == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="space-y-2">
                                    <label for="banner" class="text-sm font-semibold text-slate-700">Banner Event</label>
                                    @if($event->banner_path)
                                        <p class="text-xs text-slate-500 mb-1">Banner saat ini tersimpan. Upload baru untuk menggantinya.</p>
                                    @endif
                                    <input id="banner" name="banner" type="file" accept="image/*"
                                        class="block w-full text-xs text-slate-500 file:mr-4 file:rounded-full file:border-0 file:bg-blue-50 file:px-4 file:py-2.5 file:text-xs file:font-semibold file:text-primary hover:file:bg-blue-100">
                                </div>
                            </div>

                            <div class="space-y-2">
                                <label for="description" class="text-sm font-semibold text-slate-700">Deskripsi Lengkap</label>
                                <textarea id="description" name="description" rows="5" required
                                    class="block w-full rounded-2xl border border-slate-200 bg-white px-4 py-3.5 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-primary focus:ring-4 focus:ring-blue-100">{{ old('description', $event->description) }}</textarea>
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
                                    <input id="start_time" name="start_time" type="datetime-local"
                                        value="{{ old('start_time', $event->start_time->format('Y-m-d\TH:i')) }}" required
                                        class="block w-full rounded-2xl border border-slate-200 bg-white px-4 py-3.5 text-sm text-slate-900 outline-none transition focus:border-primary focus:ring-4 focus:ring-blue-100">
                                </div>
                                <div class="space-y-2">
                                    <label for="end_time" class="text-sm font-semibold text-slate-700">Waktu Selesai</label>
                                    <input id="end_time" name="end_time" type="datetime-local"
                                        value="{{ old('end_time', $event->end_time->format('Y-m-d\TH:i')) }}" required
                                        class="block w-full rounded-2xl border border-slate-200 bg-white px-4 py-3.5 text-sm text-slate-900 outline-none transition focus:border-primary focus:ring-4 focus:ring-blue-100">
                                </div>
                            </div>

                            <div class="space-y-2">
                                <label for="location_name" class="text-sm font-semibold text-slate-700">Nama Tempat / Venue</label>
                                <input id="location_name" name="location_name" type="text"
                                    value="{{ old('location_name', $event->location_name) }}" required
                                    class="block w-full rounded-2xl border border-slate-200 bg-white px-4 py-3.5 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-primary focus:ring-4 focus:ring-blue-100">
                            </div>

                            <div class="space-y-2">
                                <label for="address" class="text-sm font-semibold text-slate-700">Alamat Lengkap</label>
                                <textarea id="address" name="address" rows="2"
                                    class="block w-full rounded-2xl border border-slate-200 bg-white px-4 py-3.5 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-primary focus:ring-4 focus:ring-blue-100">{{ old('address', $event->address) }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Tickets -->
                    @if($event->tickets->count() > 0)
                    <div class="space-y-6">
                        <div class="flex items-center gap-3">
                            <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-50 text-sm font-bold text-primary">03</span>
                            <h3 class="text-xl font-bold text-slate-900">Tiket</h3>
                        </div>

                        <div class="space-y-4">
                            @foreach($event->tickets as $i => $ticket)
                                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                                    <input type="hidden" name="tickets[{{ $i }}][id]" value="{{ $ticket->id }}">
                                    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                                        <div class="space-y-2">
                                            <label class="text-xs font-semibold text-slate-500 uppercase tracking-widest">Nama Tiket</label>
                                            <input type="text" name="tickets[{{ $i }}][name]" value="{{ old("tickets.$i.name", $ticket->name) }}" required
                                                class="block w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-900 outline-none focus:border-primary focus:ring-2 focus:ring-blue-100">
                                        </div>
                                        <div class="space-y-2">
                                            <label class="text-xs font-semibold text-slate-500 uppercase tracking-widest">Harga (Rp)</label>
                                            <input type="number" name="tickets[{{ $i }}][price]" value="{{ old("tickets.$i.price", $ticket->price) }}" min="0" required
                                                class="block w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-900 outline-none focus:border-primary focus:ring-2 focus:ring-blue-100">
                                        </div>
                                        <div class="space-y-2">
                                            <label class="text-xs font-semibold text-slate-500 uppercase tracking-widest">Kuota</label>
                                            <input type="number" name="tickets[{{ $i }}][quota]" value="{{ old("tickets.$i.quota", $ticket->quota) }}" min="{{ $ticket->quota - $ticket->available_qty }}" required
                                                class="block w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-900 outline-none focus:border-primary focus:ring-2 focus:ring-blue-100">
                                            <p class="text-[11px] text-slate-400">{{ $ticket->quota - $ticket->available_qty }} tiket sudah terjual</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Actions -->
                    <div class="flex flex-wrap items-center justify-between gap-4 pt-6 border-t border-slate-100">
                        <a href="{{ route('organizer.events.index') }}"
                            class="px-6 py-3 rounded-2xl border border-slate-200 bg-white text-slate-700 font-semibold text-sm hover:bg-slate-50 transition">
                            Batal
                        </a>
                        <div class="flex gap-3">
                            <button type="submit" name="action" value="save"
                                class="px-6 py-3 rounded-2xl bg-slate-800 text-white font-bold text-sm hover:bg-slate-900 transition">
                                Simpan sebagai Draft
                            </button>
                            @if(in_array($event->status, ['draft', 'rejected']))
                                <button type="submit" name="action" value="submit"
                                    class="px-6 py-3 rounded-2xl bg-primary text-white font-bold text-sm hover:bg-blue-700 transition shadow-md">
                                    Ajukan untuk Review
                                </button>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
