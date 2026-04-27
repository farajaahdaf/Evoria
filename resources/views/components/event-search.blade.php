@props(['initialValue' => ''])

<div
    class="relative"
    x-data="{
        query: @js($initialValue),
        suggestions: [],
        loading: false,
        open: false,
        activeIndex: -1,
        timer: null,
        async fetchSuggestions() {
            clearTimeout(this.timer);
            this.activeIndex = -1;

            if (this.query.trim().length < 2) {
                this.suggestions = [];
                this.open = false;
                return;
            }

            this.timer = setTimeout(async () => {
                this.loading = true;
                try {
                    const response = await fetch(`{{ route('events.search-suggestions') }}?q=${encodeURIComponent(this.query.trim())}`, {
                        headers: { 'Accept': 'application/json' }
                    });
                    this.suggestions = await response.json();
                    this.open = true;
                } catch (error) {
                    this.suggestions = [];
                    this.open = false;
                } finally {
                    this.loading = false;
                }
            }, 220);
        },
        submitSearch() {
            if (this.activeIndex >= 0 && this.suggestions[this.activeIndex]) {
                window.location.href = this.suggestions[this.activeIndex].url;
                return;
            }

            if (this.query.trim()) {
                window.location.href = `{{ route('home') }}?q=${encodeURIComponent(this.query.trim())}`;
            }
        },
        move(direction) {
            if (!this.suggestions.length) return;
            this.open = true;
            this.activeIndex = (this.activeIndex + direction + this.suggestions.length) % this.suggestions.length;
        }
    }"
    @click.outside="open = false"
>
    <form @submit.prevent="submitSearch">
        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-[20px]">search</span>
        <input
            x-model="query"
            @input="fetchSuggestions"
            @focus="query.trim().length >= 2 && (open = true)"
            @keydown.arrow-down.prevent="move(1)"
            @keydown.arrow-up.prevent="move(-1)"
            @keydown.escape="open = false"
            class="w-full h-[44px] pl-11 pr-10 bg-[#F1F3F5] border-none rounded-lg text-[13px] placeholder:text-slate-400 focus:ring-1 focus:ring-primary focus:bg-white transition-colors"
            placeholder="Cari event, artis, atau lokasi..."
            type="search"
            autocomplete="off"
        />
        <span x-show="loading" x-cloak class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 text-[18px] animate-spin">progress_activity</span>
    </form>

    <div
        x-cloak
        x-show="open && query.trim().length >= 2"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 translate-y-1"
        x-transition:enter-end="opacity-100 translate-y-0"
        class="absolute left-0 right-0 top-[52px] z-[70] overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl"
    >
        <template x-if="suggestions.length > 0">
            <div class="max-h-[360px] overflow-y-auto py-2">
                <template x-for="(item, index) in suggestions" :key="item.url">
                    <a
                        :href="item.url"
                        class="flex gap-3 px-4 py-3 transition-colors"
                        :class="activeIndex === index ? 'bg-blue-50' : 'hover:bg-slate-50'"
                        @mouseenter="activeIndex = index"
                    >
                        <div class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-primary">
                            <span class="material-symbols-outlined text-[19px]">confirmation_number</span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-[13px] font-extrabold text-slate-900" x-text="item.title"></p>
                            <div class="mt-1 flex flex-wrap items-center gap-x-3 gap-y-1 text-[11px] font-semibold text-slate-500">
                                <span class="truncate" x-text="item.category"></span>
                                <span class="truncate" x-text="item.location"></span>
                                <span x-text="item.date"></span>
                            </div>
                        </div>
                        <span class="shrink-0 self-center text-[12px] font-extrabold text-primary" x-text="item.price"></span>
                    </a>
                </template>
            </div>
        </template>

        <template x-if="!loading && suggestions.length === 0">
            <div class="px-4 py-5 text-center">
                <p class="text-[13px] font-bold text-slate-700">Event tidak ditemukan</p>
                <p class="mt-1 text-[12px] text-slate-500">Coba kata kunci lain.</p>
            </div>
        </template>
    </div>
</div>
