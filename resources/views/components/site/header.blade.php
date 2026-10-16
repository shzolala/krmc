<header class="sticky top-0 z-50 bg-navy-900 text-white shadow-sm">
    <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-3.5 sm:px-6 lg:px-8">
        <a href="{{ route('home') }}" class="flex shrink-0 items-center gap-2.5">
            <span class="flex h-9 w-9 items-center justify-center rounded-full border border-white/25 bg-white/5 text-sm font-bold tracking-wide">
                KR
            </span>
            <span class="leading-tight">
                <span class="block text-sm font-bold tracking-wide">{{ config('app.name') }}</span>
                <span class="block text-[11px] font-light text-white/65">جامعه کوهنوردان کرمان</span>
            </span>
        </a>

        <nav class="hidden items-center gap-5 text-sm font-medium text-white/90 xl:flex">
            <a href="{{ route('destinations.index') }}" class="transition hover:text-accent-soft">مقاصد</a>
            <a href="{{ route('clubs.index') }}" class="transition hover:text-accent-soft">باشگاه‌ها</a>
            <a href="{{ route('coaches.index') }}" class="transition hover:text-accent-soft">مربیان</a>
            <a href="{{ route('accommodations.index') }}" class="transition hover:text-accent-soft">اقامتگاه‌ها</a>
            <a href="{{ route('shelters.index') }}" class="transition hover:text-accent-soft">پناهگاه‌ها</a>
            <a href="{{ route('shops.index') }}" class="transition hover:text-accent-soft">فروشگاه‌ها</a>
            <a href="{{ route('map') }}" class="transition hover:text-accent-soft">نقشه</a>
        </nav>

        <div class="flex items-center gap-2 sm:gap-3">
            <a href="{{ route('map') }}" class="hidden text-sm text-white/85 transition hover:text-white sm:inline">نقشه</a>
            @auth
                <a
                    href="{{ route(auth()->user()->panelHomeRoute()) }}"
                    class="rounded-full border border-white/30 px-3.5 py-2 text-xs font-semibold text-white transition hover:bg-white/10 sm:px-4 sm:text-sm"
                >
                    پنل من
                </a>
            @else
                <a
                    href="{{ route('login') }}"
                    class="rounded-full border border-white/30 px-3.5 py-2 text-xs font-semibold text-white transition hover:bg-white/10 sm:px-4 sm:text-sm"
                >
                    ورود
                </a>
            @endauth
            <a
                href="{{ route('destinations.index') }}"
                class="rounded-full bg-accent px-3.5 py-2 text-xs font-semibold text-navy-950 transition hover:bg-accent-soft sm:px-4 sm:text-sm"
            >
                پیدا کردن مقصد
            </a>
        </div>
    </div>
</header>
