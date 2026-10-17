@php
    $sections = \App\Support\SiteNavigation::sections();
@endphp

<footer class="mt-20 bg-navy-950 text-white">
    <div class="mx-auto grid max-w-7xl gap-10 px-4 py-14 sm:px-6 lg:grid-cols-[1.4fr_1fr_1fr_1fr] lg:px-8">
        <div>
            <a href="{{ route('home') }}" class="flex items-center gap-2.5">
                <span class="flex h-10 w-10 items-center justify-center rounded-full border border-white/25 bg-white/5 text-sm font-bold">KR</span>
                <span class="text-lg font-bold">{{ config('app.name') }}</span>
            </a>
            <p class="mt-4 max-w-sm text-sm leading-7 text-white/65">
                پلتفرم شفاف جامعه کوهنوردی کرمان — مقاصد، باشگاه‌ها، مربیان، اقامتگاه‌ها و پناهگاه‌ها با موقعیت جغرافیایی روی نقشه.
            </p>
            <a href="{{ route('map') }}" class="mt-5 inline-flex items-center gap-2 rounded-full bg-white/10 px-4 py-2 text-xs font-semibold ring-1 ring-white/15 transition hover:bg-accent hover:text-navy-950">
                <x-site.icon name="map" class="h-4 w-4" />
                باز کردن نقشه
            </a>
        </div>

        @foreach ([
            'گردشگری' => ['regions', 'destinations', 'attractions', 'map'],
            'کوهنوردی' => ['clubs', 'coaches', 'programs'],
            'اقامت و تجهیزات' => ['accommodations', 'shelters', 'shops'],
        ] as $heading => $keys)
            <div>
                <p class="text-sm font-semibold text-accent-soft">{{ $heading }}</p>
                <ul class="mt-4 space-y-2.5 text-sm text-white/70">
                    @foreach ($keys as $key)
                        <li><a href="{{ route($sections[$key]['route']) }}" class="transition hover:text-white">{{ $sections[$key]['label'] }}</a></li>
                    @endforeach
                </ul>
            </div>
        @endforeach
    </div>

    <div class="border-t border-white/10">
        <div class="mx-auto flex max-w-7xl flex-col gap-2 px-4 py-5 text-xs text-white/50 sm:flex-row sm:items-center sm:justify-between sm:px-6 lg:px-8">
            <p>© {{ fa_num(now()->year) }} {{ config('app.name') }} — همه حقوق محفوظ است.</p>
            <p>تبلیغات با شفافیت مالی · پناهگاه‌ها جدا از اقامتگاه‌های تجاری</p>
        </div>
    </div>
</footer>
