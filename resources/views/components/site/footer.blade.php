<footer class="mt-20 border-t border-navy-900/10 bg-navy-900 text-white">
    <div class="mx-auto grid max-w-7xl gap-10 px-4 py-14 sm:px-6 lg:grid-cols-4 lg:px-8">
        <div class="lg:col-span-2">
            <p class="text-lg font-bold">{{ config('app.name') }}</p>
            <p class="mt-3 max-w-md text-sm leading-7 text-white/70">
                پلتفرم شفاف جامعه کوهنوردی کرمان — مقاصد، باشگاه‌ها، اقامتگاه‌ها و پناهگاه‌ها با موقعیت جغرافیایی روی نقشه.
            </p>
        </div>

        <div>
            <p class="text-sm font-semibold text-accent-soft">دسترسی سریع</p>
            <ul class="mt-4 space-y-2 text-sm text-white/75">
                <li><a href="{{ route('destinations.index') }}" class="hover:text-white">مقاصد تخصصی</a></li>
                <li><a href="{{ route('accommodations.index') }}" class="hover:text-white">اقامتگاه‌ها</a></li>
                <li><a href="{{ route('shelters.index') }}" class="hover:text-white">پناهگاه‌های کرمان</a></li>
                <li><a href="{{ route('map') }}" class="hover:text-white">نقشه یکپارچه</a></li>
            </ul>
        </div>

        <div>
            <p class="text-sm font-semibold text-accent-soft">شفافیت</p>
            <p class="mt-4 text-sm leading-7 text-white/75">
                تبلیغات و دیده‌شدن با شفافیت مالی انجام می‌شود. پناهگاه‌ها جدا از اقامتگاه‌های تجاری نمایش داده می‌شوند.
            </p>
        </div>
    </div>

    <div class="border-t border-white/10">
        <div class="mx-auto flex max-w-7xl flex-col gap-2 px-4 py-5 text-xs text-white/50 sm:flex-row sm:items-center sm:justify-between sm:px-6 lg:px-8">
            <p>© {{ now()->year }} {{ config('app.name') }} — همه حقوق محفوظ است.</p>
            <p>مبتنی بر موقعیت جغرافیایی</p>
        </div>
    </div>
</footer>
