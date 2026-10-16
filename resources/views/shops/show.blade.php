<x-layouts.site :title="$shop->name">
    <section class="bg-navy-900 text-white">
        <div class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
            <p class="text-sm text-white/60">خانه / فروشگاه‌ها / {{ $shop->name }}</p>
            <h1 class="mt-3 text-4xl font-extrabold">{{ $shop->name }}</h1>
            <p class="mt-3 max-w-2xl text-sm leading-8 text-white/75">{{ $shop->summary }}</p>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="rounded-2xl bg-white p-6 ring-1 ring-navy-900/5">
            <p class="text-sm leading-8 text-muted">پروفایل فروشگاه — جزئیات محصولات، تبلیغات و پلن نمایش در گام‌های بعدی تکمیل می‌شود.</p>
            <p class="mt-4 text-sm text-muted">شهر: {{ $shop->city }}</p>
        </div>

        <div class="mt-10">
            <livewire:review.section :reviewable="$shop" :key="'shop-reviews-'.$shop->id" />
        </div>
    </section>
</x-layouts.site>
