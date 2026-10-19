@php
    $sections = \App\Support\SiteNavigation::sections();
    $primarySections = collect($sections)->where('primary', true);
    $secondarySections = collect($sections)->where('primary', false);
    $accountLink = auth()->check()
        ? ['label' => 'پنل من', 'href' => route(auth()->user()->panelHomeRoute())]
        : ['label' => 'ورود / ثبت‌نام', 'href' => route('login')];
@endphp

<header class="sticky top-0 z-50 bg-sand/90 text-navy-900 backdrop-blur-md">
    <div class="mx-auto flex max-w-[120rem] items-center justify-between gap-4 px-4 py-3 sm:px-5">
        <a href="{{ route('home') }}" class="flex shrink-0 items-center gap-2.5">
            <span class="flex h-9 w-9 items-center justify-center rounded-full bg-white text-sm font-bold tracking-wide shadow-sm ring-1 ring-navy-900/10">KR</span>
            <span class="leading-tight">
                <span class="block text-sm font-bold tracking-wide">{{ config('app.name') }}</span>
                <span class="block text-[11px] font-light text-muted">جامعه کوهنوردان کرمان</span>
            </span>
        </a>

        <nav class="hidden items-center gap-2 text-sm font-medium text-navy-800 lg:flex" aria-label="ناوبری اصلی">
            @foreach ($primarySections as $section)
                @php($isActive = request()->routeIs(str_replace('.index', '.*', $section['route'])))
                <a href="{{ route($section['route']) }}" @if ($isActive) aria-current="page" @endif @class([
                    'relative px-3.5 pb-2.5 pt-2 transition-colors after:absolute after:inset-x-3.5 after:bottom-1 after:h-0.5 after:rounded-full after:bg-accent after:transition-transform after:duration-200',
                    'font-bold text-navy-900 after:scale-x-100' => $isActive,
                    'text-navy-800/75 after:scale-x-0 hover:text-navy-900 hover:after:scale-x-50' => ! $isActive,
                ])>{{ $section['label'] }}</a>
            @endforeach

            <div class="group relative">
                <button type="button" class="flex items-center gap-1 rounded-full px-3.5 py-2 text-navy-800/75 transition-colors hover:text-navy-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-accent" aria-haspopup="true">
                    بیشتر
                    <x-site.icon name="chevron-down" class="h-3.5 w-3.5 transition group-hover:rotate-180 group-focus-within:rotate-180" />
                </button>
                <div class="invisible absolute left-0 top-full z-50 w-64 pt-2 opacity-0 transition group-focus-within:visible group-focus-within:opacity-100 group-hover:visible group-hover:opacity-100">
                    <ul class="overflow-hidden rounded-2xl bg-white p-2 text-navy-900 shadow-2xl shadow-navy-950/30 ring-1 ring-navy-900/10">
                        @foreach ($secondarySections as $section)
                            <li>
                                <a href="{{ route($section['route']) }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 transition hover:bg-sand">
                                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-accent/15 text-accent">
                                        <x-site.icon :name="$section['icon']" class="h-[18px] w-[18px]" />
                                    </span>
                                    <span>
                                        <span class="block text-sm font-bold">{{ $section['label'] }}</span>
                                        <span class="block text-[11px] text-muted">{{ $section['description'] }}</span>
                                    </span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </nav>

        <div class="flex items-center gap-2">
            <a href="{{ route('destinations.index') }}" class="flex h-10 w-10 items-center justify-center rounded-full text-navy-800 transition hover:bg-navy-900/5 hover:text-navy-900" aria-label="جستجو در مقاصد">
                <x-site.icon name="search" />
            </a>
            <a href="{{ $accountLink['href'] }}" class="hidden rounded-full bg-navy-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-navy-700 sm:inline-flex">
                {{ $accountLink['label'] }}
            </a>

            <details class="group/menu lg:hidden">
                <summary class="flex h-10 w-10 cursor-pointer list-none items-center justify-center rounded-full bg-white shadow-sm ring-1 ring-navy-900/10 transition hover:bg-navy-900/5 [&::-webkit-details-marker]:hidden" aria-label="منو">
                    <x-site.icon name="menu" class="group-open/menu:hidden" />
                    <x-site.icon name="close" class="hidden group-open/menu:block" />
                </summary>
                <div class="absolute inset-x-0 top-full max-h-[calc(100dvh-4rem)] overflow-y-auto border-t border-navy-900/10 bg-sand px-4 pb-6 pt-4 shadow-2xl sm:px-6">
                    <ul class="mx-auto grid max-w-7xl grid-cols-2 gap-2 sm:grid-cols-3">
                        @foreach ($sections as $section)
                            <li>
                                <a href="{{ route($section['route']) }}" class="flex items-center gap-3 rounded-2xl bg-white px-3 py-3 ring-1 ring-navy-900/5 transition hover:bg-navy-900/5">
                                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-accent/15 text-accent">
                                        <x-site.icon :name="$section['icon']" class="h-[18px] w-[18px]" />
                                    </span>
                                    <span class="text-sm font-semibold">{{ $section['label'] }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                    <a href="{{ $accountLink['href'] }}" class="mx-auto mt-4 block max-w-7xl rounded-full bg-navy-900 px-4 py-3 text-center text-sm font-bold text-white sm:hidden">
                        {{ $accountLink['label'] }}
                    </a>
                </div>
            </details>
        </div>
    </div>
</header>
