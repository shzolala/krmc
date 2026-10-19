@php
    $sections = \App\Support\SiteNavigation::sections();
@endphp

<footer class="mt-16 bg-navy-950 text-white sm:mt-20">
    <div class="mx-auto grid max-w-[120rem] gap-8 px-4 py-10 sm:gap-10 sm:px-5 sm:py-14 lg:grid-cols-[1.4fr_1fr_1fr_1fr]">
        <div>
            <div class="flex items-center justify-between gap-4 lg:block">
                <a href="{{ route('home') }}" class="flex items-center gap-2.5">
                    <span class="flex h-9 w-9 items-center justify-center rounded-full border border-white/25 bg-white/5 text-xs font-bold sm:h-10 sm:w-10 sm:text-sm">KR</span>
                    <span class="text-base font-bold sm:text-lg">{{ config('app.name') }}</span>
                </a>
                <a href="{{ route('map') }}" class="inline-flex items-center gap-2 rounded-full bg-white/10 px-3.5 py-2 text-xs font-semibold ring-1 ring-white/15 transition hover:bg-accent hover:text-navy-950 lg:mt-5 lg:px-4">
                    <x-site.icon name="map" class="h-4 w-4" />
                    باز کردن نقشه
                </a>
            </div>
            <p class="mt-4 max-w-sm text-[13px] leading-7 text-white/60 sm:text-sm">
                پلتفرم شفاف جامعه کوهنوردی کرمان — مقاصد، باشگاه‌ها، مربیان، اقامتگاه‌ها و پناهگاه‌ها با موقعیت جغرافیایی روی نقشه.
            </p>
        </div>

        <div class="grid grid-cols-3 gap-x-4 border-t border-white/10 pt-8 lg:contents lg:border-0 lg:pt-0">
            @foreach ([
                'گردشگری' => ['regions', 'destinations', 'attractions', 'peaks', 'map'],
                'کوهنوردی' => ['clubs', 'coaches', 'programs'],
                'اقامت و تجهیزات' => ['accommodations', 'shelters', 'shops'],
            ] as $heading => $keys)
                <nav aria-label="{{ $heading }}">
                    <p class="text-[13px] font-semibold text-accent-soft sm:text-sm">{{ $heading }}</p>
                    <ul class="mt-3 space-y-2 text-[13px] text-white/70 sm:mt-4 sm:space-y-2.5 sm:text-sm">
                        @foreach ($keys as $key)
                            <li><a href="{{ route($sections[$key]['route']) }}" class="transition hover:text-white">{{ $sections[$key]['label'] }}</a></li>
                        @endforeach
                        @if ($heading === 'کوهنوردی')
                            <li><a href="{{ route('projects.index') }}" class="transition hover:text-white">طرح‌های حمایتی</a></li>
                            <li><a href="{{ route('sponsors.index') }}" class="transition hover:text-white">حامیان کوهستان</a></li>
                            <li><a href="{{ route('pricing') }}" class="text-white/50 transition hover:text-white">تعرفه عضویت</a></li>
                        @endif
                    </ul>
                </nav>
            @endforeach
        </div>
    </div>

    <div class="border-t border-white/10">
        <div class="mx-auto flex max-w-[120rem] flex-col gap-1.5 px-4 py-4 text-[11px] leading-6 text-white/45 sm:flex-row sm:items-center sm:justify-between sm:px-5 sm:py-5 sm:text-xs">
            <p>© {{ fa_digits(\App\Support\JalaliDate::currentYear()) }} {{ config('app.name') }} — همه حقوق محفوظ است.</p>
            <p>تبلیغات با شفافیت مالی · پناهگاه‌ها جدا از اقامتگاه‌های تجاری</p>
        </div>
    </div>
</footer>
