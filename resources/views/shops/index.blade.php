<x-layouts.site title="فروشگاه‌ها">
    <section class="bg-navy-900 text-white">
        <div class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
            <p class="text-sm text-white/60">خانه / فروشگاه‌ها</p>
            <h1 class="mt-3 text-4xl font-extrabold">فروشگاه‌های تجهیزات</h1>
            <p class="mt-3 max-w-2xl text-sm leading-8 text-white/75">{{ $shops->total() }} فروشگاه مرتبط با جامعه کوهنوردی کرمان.</p>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="grid gap-4 md:grid-cols-2">
            @foreach ($shops as $shop)
                <article class="rounded-2xl border border-navy-900/8 bg-white p-5">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h2 class="text-lg font-bold text-navy-900">
                                <a href="{{ route('shops.show', $shop) }}" class="hover:text-accent">{{ $shop->name }}</a>
                            </h2>
                            <p class="mt-1 text-xs text-muted">{{ $shop->city }}</p>
                        </div>
                        @if ($shop->is_verified)<x-badge tone="verified">تأییدشده</x-badge>@endif
                    </div>
                    <p class="mt-3 text-sm leading-7 text-muted">{{ $shop->summary }}</p>
                </article>
            @endforeach
        </div>
        <div class="mt-8">{{ $shops->links() }}</div>
    </section>
</x-layouts.site>
