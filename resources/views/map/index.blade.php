@php
    $markerList = collect($mapMarkers)->filter()->values();
    $centerLat = $markerList->avg('lat') ?: 30.2839;
    $centerLng = $markerList->avg('lng') ?: 57.0834;
    $typeLabels = collect($layers)->mapWithKeys(fn (array $layer, string $type) => [$type => $layer['singular']])->all();
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
        data-share-endpoint="{{ route('map.share') }}"
        data-csrf="{{ csrf_token() }}"
        data-type-labels='@json($typeLabels, JSON_UNESCAPED_UNICODE)'
    >
        <h1 class="sr-only">نقشه یکپارچه کوهنوردی کرمان</h1>

        <div class="relative z-20 shrink-0">
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

                <div class="relative" data-map-layers-menu>
                    <button
                        type="button"
                        data-map-layers-toggle
                        aria-expanded="false"
                        class="inline-flex cursor-pointer items-center gap-2 rounded-xl bg-navy-900 px-3.5 py-2 text-xs font-semibold text-white transition hover:bg-navy-800"
                    >
                        <span>لایه‌ها</span>
                        <span data-map-layer-summary class="rounded-full bg-white/20 px-1.5 py-0.5 text-[10px]">{{ fa_num(count($layers)) }}</span>
                        <svg class="map-layers-caret h-3.5 w-3.5 transition" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M5.5 7.5 10 12l4.5-4.5-1-1L10 10 6.5 6.5z"/></svg>
                    </button>

                    <div
                        data-map-layers-panel
                        hidden
                        class="absolute start-0 top-full z-50 mt-2 w-64 rounded-xl bg-white p-1 shadow-xl shadow-navy-900/15 ring-1 ring-navy-900/10"
                    >
                        <button
                            type="button"
                            data-map-layer-all
                            aria-pressed="true"
                            class="map-explorer-layer map-explorer-layer--all is-active mb-1 flex w-full cursor-pointer items-center gap-2.5 rounded-lg border-b border-navy-900/5 px-2.5 py-1.5 text-[13px] font-bold text-ink transition hover:bg-sand"
                        >
                            <span class="map-explorer-layer__check flex h-4 w-4 shrink-0 items-center justify-center rounded ring-1 ring-navy-900/25">
                                <svg class="map-explorer-layer__tick h-2.5 w-2.5" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m4.5 10.5 3.5 3.5 7.5-8"/></svg>
                                <svg class="map-explorer-layer__dash h-2.5 w-2.5" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" aria-hidden="true"><path d="M5 10h10"/></svg>
                            </span>
                            <span class="flex-1 text-start">همه</span>
                            <span class="text-xs font-normal text-muted">{{ fa_num(array_sum($counts)) }}</span>
                        </button>
                        @foreach ($layers as $type => $layer)
                            <div class="map-explorer-layer-row flex items-center gap-0.5">
                                <button
                                    type="button"
                                    data-map-layer="{{ $type }}"
                                    aria-pressed="true"
                                    class="map-explorer-layer is-active flex min-w-0 flex-1 cursor-pointer items-center gap-2.5 rounded-lg px-2.5 py-1.5 text-[13px] text-ink transition hover:bg-sand"
                                >
                                    <span class="map-explorer-layer__check flex h-4 w-4 shrink-0 items-center justify-center rounded ring-1 ring-navy-900/25">
                                        <svg class="h-2.5 w-2.5" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m4.5 10.5 3.5 3.5 7.5-8"/></svg>
                                    </span>
                                    <span class="h-2 w-2 shrink-0 rounded-full" style="background: {{ $layer['color'] }}"></span>
                                    <span class="flex-1 truncate text-start font-medium">{{ $layer['label'] }}</span>
                                    <span class="text-xs text-muted">{{ fa_num($counts[$type] ?? 0) }}</span>
                                </button>
                                <button type="button" data-map-layer-only="{{ $type }}" title="نمایش فقط {{ $layer['label'] }}" aria-label="نمایش فقط {{ $layer['label'] }}" aria-pressed="false" class="map-explorer-layer__solo cursor-pointer rounded-md p-1.5 text-muted transition hover:bg-sand hover:text-navy-900 focus-visible:opacity-100">
                                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m11 17-5-5 5-5M18 17l-5-5 5-5"/></svg>
                                </button>
                            </div>
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

            <section data-map-stage class="map-explorer__stage relative isolate min-h-0 lg:col-span-5 xl:col-span-5">
                <div data-map-canvas dir="ltr" class="absolute inset-0"></div>

                <div class="pointer-events-none absolute inset-x-3 top-3 z-[500] flex justify-between gap-2">
                    <div class="pointer-events-auto flex overflow-hidden rounded-xl bg-white/95 shadow-lg shadow-navy-900/10 ring-1 ring-navy-900/10">
                        <button type="button" data-map-base="topo" class="map-explorer-base is-active cursor-pointer px-3 py-2 text-xs font-semibold transition">توپوگرافی</button>
                        <button type="button" data-map-base="satellite" class="map-explorer-base cursor-pointer px-3 py-2 text-xs font-semibold transition">ماهواره</button>
                        <button type="button" data-map-base="streets" class="map-explorer-base cursor-pointer px-3 py-2 text-xs font-semibold transition">ساده</button>
                    </div>

                    <div class="pointer-events-auto flex gap-2">
                    <button
                        type="button"
                        data-map-locate
                        class="pointer-events-auto cursor-pointer rounded-xl bg-white/95 px-3 py-2 text-xs font-semibold text-navy-900 shadow-lg shadow-navy-900/10 ring-1 ring-navy-900/10 transition hover:bg-sand"
                    >
                        موقعیت من
                    </button>
                    <button
                        type="button"
                        data-map-share-open
                        class="pointer-events-auto inline-flex cursor-pointer items-center gap-1.5 rounded-xl bg-navy-900 px-3 py-2 text-xs font-bold text-white shadow-lg shadow-navy-900/20 transition hover:bg-navy-800"
                    >
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><path d="m8.6 13.5 6.8 4M15.4 6.5l-6.8 4"/></svg>
                        اشتراک‌گذاری
                    </button>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <dialog data-map-share class="map-share" aria-labelledby="map-share-title">
        <form method="dialog" class="map-share__close"><button aria-label="بستن">×</button></form>
        <h2 id="map-share-title" class="text-base font-extrabold text-navy-900">اشتراک‌گذاری این نقشه</h2>
        <p class="mt-1 text-xs text-muted">لینک دقیقاً همین حالت نقشه را برای دیگران باز می‌کند.</p>

        <div class="mt-4 grid gap-2 text-sm text-ink">
            <label class="map-share__option"><input type="checkbox" data-share-opt="view" checked> محدودهٔ فعلی نقشه (مرکز و زوم)</label>
            <label class="map-share__option" data-share-opt-wrap="types"><input type="checkbox" data-share-opt="types" checked> <span>فقط لایه‌های انتخاب‌شده</span></label>
            <label class="map-share__option" data-share-opt-wrap="focus"><input type="checkbox" data-share-opt="focus" checked> <span>مکان انتخاب‌شده: <b data-share-focus-name></b></span></label>
            <label class="map-share__option" data-share-opt-wrap="q"><input type="checkbox" data-share-opt="q" checked> <span>جستجوی «<b data-share-q-text></b>»</span></label>
            <div class="map-share__bases" role="radiogroup" aria-label="نوع نقشه">
                <span>نوع نقشه:</span>
                <label><input type="radio" name="share-base" data-share-base value="topo" checked> توپوگرافی</label>
                <label><input type="radio" name="share-base" data-share-base value="satellite"> ماهواره</label>
                <label><input type="radio" name="share-base" data-share-base value="streets"> ساده</label>
            </div> 
        </div>

        <div class="mt-4 flex items-center gap-3">
            <div data-share-qr class="map-share__qr" aria-hidden="true"></div>
            <div class="min-w-0 flex-1">
                <input readonly dir="ltr" data-share-url class="w-full rounded-lg border-0 bg-sand px-3 py-2 font-mono text-xs text-ink ring-1 ring-navy-900/10" value="">
                <div class="mt-2 flex flex-wrap gap-1.5">
                    <button type="button" data-share-copy class="map-share__btn map-share__btn--primary">کپی لینک</button>
                    <button type="button" data-share-native class="map-share__btn" hidden>ارسال…</button>
                    <a data-share-telegram target="_blank" rel="noopener" class="map-share__btn">تلگرام</a>
                    <a data-share-whatsapp target="_blank" rel="noopener" class="map-share__btn">واتساپ</a>
                    <a data-share-download class="map-share__btn">QR</a>
                </div>
            </div>
        </div>
        <p data-share-status class="mt-3 min-h-4 text-xs text-muted" role="status"></p>
    </dialog>
</x-layouts.site>
