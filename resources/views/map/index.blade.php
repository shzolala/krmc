@php
    $markerList = collect($mapMarkers)->filter()->values();
    $centerLat = $markerList->avg('lat') ?: 30.2839;
    $centerLng = $markerList->avg('lng') ?: 57.0834;
    $typeLabels = collect($layers)->mapWithKeys(fn (array $layer, string $type) => [$type => $layer['label']])->all();
@endphp

<x-layouts.site
    title="نقشه"
    meta-description="نقشه تعاملی مقاصد، اقامتگاه‌ها، پناهگاه‌ها، برنامه‌ها، باشگاه‌ها و فروشگاه‌های جامعه کوهنوردان کرمان."
    :hide-footer="true"
>
    @push('head')
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
    @endpush

    @push('scripts')
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    @endpush

    <div
        class="map-explorer flex flex-col bg-sand"
        data-map-explorer
        data-center-lat="{{ $centerLat }}"
        data-center-lng="{{ $centerLng }}"
        data-markers='@json($markerList, JSON_UNESCAPED_UNICODE)'
        data-type-labels='@json($typeLabels, JSON_UNESCAPED_UNICODE)'
    >
        <h1 class="sr-only">نقشه یکپارچه کوهنوردی کرمان</h1>

        <div class="shrink-0">
            <div class="mx-auto flex max-w-[1800px] flex-wrap items-center gap-2 px-3 py-2 sm:px-5">
                <label class="relative block w-full sm:w-80 lg:w-96" data-map-search-wrap>
                    <span class="sr-only">جستجو روی نقشه</span>
                    <input
                        type="search"
                        data-map-search
                        placeholder="جستجوی مقصد، اقامتگاه، باشگاه..."
                        class="w-full cursor-text rounded-xl border-0 bg-white px-4 py-2 text-sm text-ink outline-none ring-1 ring-navy-900/10 transition placeholder:text-muted/70 focus:ring-2 focus:ring-accent"
                    >
                </label>

                <label data-map-sort-wrap class="hidden cursor-pointer items-center gap-2 rounded-xl bg-white px-3 py-2 text-xs font-medium text-muted ring-1 ring-navy-900/10 transition hover:bg-white hover:ring-navy-900/25 sm:inline-flex">
                    <span>مرتب‌سازی</span>
                    <select
                        data-map-sort
                        class="cursor-pointer border-0 bg-transparent p-0 text-sm font-semibold text-ink outline-none"
                    >
                        <option value="featured">پیشنهادی</option>
                        <option value="nearest">نزدیک به مرکز نقشه</option>
                        <option value="name">نام</option>
                    </select>
                </label>

                <button
                    type="button"
                    data-map-fit
                    class="hidden cursor-pointer items-center rounded-xl bg-white px-3 py-2 text-xs font-semibold text-navy-900 ring-1 ring-navy-900/10 transition hover:bg-sand hover:ring-accent/50 lg:inline-flex"
                >
                    نمایش همه روی نقشه
                </button>

                <div class="relative" x-data="{ open: false }" @click.outside="open = false" @keydown.escape.window="open = false">
                    <button
                        type="button"
                        @click="open = ! open"
                        :aria-expanded="open"
                        class="inline-flex cursor-pointer items-center gap-2 rounded-xl bg-navy-900 px-3.5 py-2 text-xs font-semibold text-white transition hover:bg-navy-800"
                    >
                        <span>لایه‌ها</span>
                        <span data-map-layer-summary class="rounded-full bg-white/20 px-1.5 py-0.5 text-[10px]">{{ fa_num(count($layers)) }}</span>
                        <svg class="h-3.5 w-3.5 transition" :class="open && 'rotate-180'" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M5.5 7.5 10 12l4.5-4.5-1-1L10 10 6.5 6.5z"/></svg>
                    </button>

                    <div
                        x-show="open"
                        x-cloak
                        x-transition.opacity
                        class="absolute start-0 top-full z-[1000] mt-2 w-64 rounded-2xl bg-white p-1.5 shadow-xl shadow-navy-900/15 ring-1 ring-navy-900/10"
                    >
                        @foreach ($layers as $type => $layer)
                            <button
                                type="button"
                                data-map-layer="{{ $type }}"
                                aria-pressed="true"
                                class="map-explorer-layer is-active flex w-full cursor-pointer items-center gap-3 rounded-xl px-3 py-2 text-sm text-ink transition hover:bg-sand"
                            >
                                <span class="map-explorer-layer__check flex h-5 w-5 shrink-0 items-center justify-center rounded-md ring-1 ring-navy-900/25">
                                    <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m4.5 10.5 3.5 3.5 7.5-8"/></svg>
                                </span>
                                <span class="h-2.5 w-2.5 shrink-0 rounded-full" style="background: {{ $layer['color'] }}"></span>
                                <span class="flex-1 text-start font-medium">{{ $layer['label'] }}</span>
                                <span class="text-xs text-muted">{{ fa_num($counts[$type] ?? 0) }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>

                <button
                    type="button"
                    class="ms-auto inline-flex shrink-0 cursor-pointer items-center rounded-xl bg-accent px-4 py-2 text-xs font-bold text-navy-950 transition hover:bg-accent-soft lg:hidden"
                    data-map-mobile-toggle
                >
                    مشاهده نقشه
                </button>
            </div>
        </div>

        <div class="map-explorer__body mx-auto grid min-h-0 w-full max-w-[1800px] flex-1 grid-cols-1 grid-rows-[minmax(0,1fr)] lg:grid-cols-12">
            <section data-map-panel class="map-explorer__panel min-h-0 px-3 py-3 sm:px-5 lg:col-span-7 xl:col-span-7">
                <div data-map-panel-header class="mb-2 flex items-center justify-between gap-3 text-xs text-muted">
                    <span>مکان‌های داخل محدودهٔ نقشه</span>
                    <span class="rounded-full bg-white px-2.5 py-1 font-semibold text-navy-900 ring-1 ring-navy-900/10" data-map-count>
                        {{ fa_num($markerList->count()) }} مورد
                    </span>
                </div>

                <div data-map-list class="map-explorer__list grid grid-cols-1 gap-2.5 sm:grid-cols-2"></div>

                <p data-map-empty class="mt-6 hidden rounded-2xl bg-white p-6 text-center text-sm text-muted ring-1 ring-navy-900/5">
                    در این محدوده موردی نیست. نقشه را جابه‌جا کنید، زوم‌اوت کنید یا فیلترها را تغییر دهید.
                </p>
            </section>

            <section data-map-stage class="map-explorer__stage relative min-h-0 lg:col-span-5 xl:col-span-5">
                <div data-map-canvas dir="ltr" class="absolute inset-0"></div>

                <div class="pointer-events-none absolute inset-x-3 top-3 z-[500] flex justify-between gap-2">
                    <div class="pointer-events-auto flex overflow-hidden rounded-xl bg-white/95 shadow-lg shadow-navy-900/10 ring-1 ring-navy-900/10">
                        <button type="button" data-map-base="streets" class="map-explorer-base is-active cursor-pointer px-3 py-2 text-xs font-semibold transition">خیابان</button>
                        <button type="button" data-map-base="satellite" class="map-explorer-base cursor-pointer px-3 py-2 text-xs font-semibold transition">ماهواره</button>
                    </div>

                    <button
                        type="button"
                        data-map-locate
                        class="pointer-events-auto cursor-pointer rounded-xl bg-white/95 px-3 py-2 text-xs font-semibold text-navy-900 shadow-lg shadow-navy-900/10 ring-1 ring-navy-900/10 transition hover:bg-sand"
                    >
                        موقعیت من
                    </button>
                </div>
            </section>
        </div>
    </div>
</x-layouts.site>
