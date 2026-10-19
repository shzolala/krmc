@props([
    'slides',
    'ongoingCount' => 0,
])

{{--
    Full-width home slider. Slides come from the admin panel (HeroSlide); each one can point to a
    program, destination or region of this site, shown as the small caption bar at the bottom.
--}}
@php
    $items = collect($slides)->map(fn ($slide): array => [
        'image' => $slide->image_url,
        'title' => $slide->title,
        'subtitle' => $slide->subtitle,
        'target' => $slide->target(),
    ]);

    if ($items->isEmpty()) {
        $items = collect([[
            'image' => asset('images/covers/hero.jpg'),
            'title' => 'جامعه کوهنوردان کرمان',
            'subtitle' => 'برنامه‌های صعود معتبر کرمان، با باشگاه و سرپرست تأییدشده.',
            'target' => null,
        ]]);
    }

    $multiple = $items->count() > 1;
@endphp

<section {{ $attributes->class(['mx-auto max-w-[120rem] px-4 pt-1 sm:px-5']) }} data-hero-slider aria-roledescription="carousel" aria-label="اسلایدر صفحه اول">
    <div class="relative isolate overflow-hidden rounded-3xl bg-navy-950 text-white shadow-xl shadow-navy-900/15">
        <div class="relative h-[34rem] sm:h-[36rem] lg:h-[40rem]">
            @foreach ($items as $slide)
                <div
                    data-hero-slide
                    @if ($loop->first) data-active="true" @else inert @endif
                    role="group"
                    aria-roledescription="slide"
                    aria-label="{{ fa_num($loop->iteration) }} از {{ fa_num($items->count()) }}"
                    class="absolute inset-0 opacity-0 transition-opacity duration-1000 data-[active=true]:opacity-100 motion-reduce:transition-none"
                >
                    <img src="{{ $slide['image'] }}" alt="" @if (! $loop->first) loading="lazy" @endif class="absolute inset-0 -z-20 h-full w-full object-cover">
                    <div class="hero-overlay absolute inset-0 -z-10"></div>
                    <div class="topo-lines absolute inset-0 -z-10"></div>

                    <div class="flex h-full max-w-4xl flex-col justify-center px-5 pb-24 pt-8 sm:justify-end sm:px-10 sm:pb-32 sm:pt-0 lg:px-14">
                        @if ($ongoingCount > 0)
                            <a href="{{ route('programs.index', ['status' => 'ongoing']) }}" class="mb-4 inline-flex w-fit items-center gap-2 rounded-full bg-white/10 py-1.5 pl-3 pr-3 text-[0.7rem] sm:mb-5 sm:gap-2.5 sm:pl-4 sm:text-xs font-semibold ring-1 ring-white/15 backdrop-blur transition hover:bg-white/15">
                                <span class="relative flex h-2 w-2">
                                    <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-400 opacity-75"></span>
                                    <span class="relative inline-flex h-2 w-2 rounded-full bg-emerald-400"></span>
                                </span>
                                {{ fa_num($ongoingCount) }} برنامه در حال برگزاری
                                <x-site.icon name="arrow-left" class="h-3.5 w-3.5" />
                            </a>
                        @endif

                        @if ($loop->first)
                            <h1 class="text-3xl font-extrabold leading-[1.4] text-balance sm:text-5xl lg:text-6xl">{{ $slide['title'] }}</h1>
                        @else
                            <h2 class="text-3xl font-extrabold leading-[1.4] text-balance sm:text-5xl lg:text-6xl">{{ $slide['title'] }}</h2>
                        @endif

                        @if ($slide['subtitle'])
                            <p class="mt-3 max-w-xl text-sm leading-7 text-white/90 sm:mt-4 sm:text-lg sm:leading-8">{{ $slide['subtitle'] }}</p>
                        @endif

                        <div class="mt-6 flex flex-wrap items-center gap-2.5 sm:mt-7 sm:gap-3">
                            <x-site.button :href="route('programs.index')" variant="primary" icon="calendar">برنامه‌های پیش‌رو</x-site.button>
                            <x-site.button :href="route('clubs.index')" variant="secondary" icon="users">پیدا کردن باشگاه</x-site.button>
                        </div>
                    </div>
                </div>
            @endforeach

            {{-- Floating glass chips over the photo: where this slide points to (right) and the slide controls (left). --}}
            <div class="pointer-events-none absolute inset-x-3 bottom-3 flex items-center justify-between gap-2 sm:items-end sm:gap-3 sm:inset-x-6 sm:bottom-6">
                <div class="grid min-w-0 max-w-[68%]">
                    @foreach ($items as $slide)
                        <div data-hero-caption @if ($loop->first) data-active="true" @endif class="invisible col-start-1 row-start-1 min-w-0 translate-y-2 opacity-0 transition duration-500 data-[active=true]:visible data-[active=true]:translate-y-0 data-[active=true]:opacity-100">
                            @if ($slide['target'])
                                <a href="{{ $slide['target']['url'] }}" @unless ($loop->first) tabindex="-1" @endunless class="group pointer-events-auto inline-flex max-w-full items-center gap-2 rounded-full bg-white/15 py-1.5 pl-3 pr-2 text-xs sm:gap-2.5 sm:py-2 sm:pl-4 sm:pr-3 sm:text-sm font-semibold shadow-lg shadow-black/10 ring-1 ring-white/25 backdrop-blur-md transition hover:bg-white/25 focus:outline-none focus-visible:ring-2 focus-visible:ring-accent">
                                    <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-accent sm:h-7 sm:w-7 text-navy-950">
                                        <x-site.icon name="pin" class="h-4 w-4" />
                                    </span>
                                    <span class="shrink-0 text-white/70">{{ $slide['target']['kind'] }}</span>
                                    <span class="truncate">{{ $slide['target']['label'] }}</span>
                                    <x-site.icon name="arrow-left" class="h-3.5 w-3.5 shrink-0 transition group-hover:-translate-x-1" />
                                </a>
                            @endif
                        </div>
                    @endforeach
                </div>

                @if ($multiple)
                    <div class="pointer-events-auto flex shrink-0 items-center gap-3 rounded-full bg-white/15 px-2 py-2 sm:p-1.5 shadow-lg shadow-black/10 ring-1 ring-white/25 backdrop-blur-md">
                        <button type="button" data-hero-prev aria-label="اسلاید قبلی" class="hidden h-8 w-8 items-center justify-center rounded-full transition hover:bg-white/20 focus:outline-none focus-visible:ring-2 focus-visible:ring-accent sm:flex">
                            <x-site.icon name="arrow-left" class="h-4 w-4 rotate-180" />
                        </button>
                        <div class="flex items-center gap-1.5 sm:px-0" role="tablist" aria-label="انتخاب اسلاید">
                            @foreach ($items as $slide)
                                <button type="button" data-hero-dot aria-label="اسلاید {{ fa_num($loop->iteration) }}" @if ($loop->first) aria-current="true" @endif class="h-1.5 w-1.5 rounded-full bg-white/50 transition-all hover:bg-white/80 focus:outline-none focus-visible:ring-2 focus-visible:ring-accent aria-[current=true]:w-6 aria-[current=true]:bg-accent"></button>
                            @endforeach
                        </div>
                        <button type="button" data-hero-next aria-label="اسلاید بعدی" class="hidden h-8 w-8 items-center justify-center rounded-full transition hover:bg-white/20 focus:outline-none focus-visible:ring-2 focus-visible:ring-accent sm:flex">
                            <x-site.icon name="arrow-left" class="h-4 w-4" />
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
