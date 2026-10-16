<x-layouts.site title="باشگاه‌ها">
    <section class="bg-navy-900 text-white">
        <div class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
            <p class="text-sm text-white/60">خانه / باشگاه‌ها</p>
            <h1 class="mt-3 text-4xl font-extrabold">باشگاه‌های کوهنوردی کرمان</h1>
            <p class="mt-3 max-w-2xl text-sm leading-8 text-white/75">{{ $clubs->total() }} باشگاه در دایرکتوری — با پروفایل، اعضا و برنامه‌ها.</p>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="space-y-4">
            @foreach ($clubs as $club)
                <article class="rounded-2xl border border-navy-900/8 bg-white p-5">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div>
                            <h2 class="text-xl font-bold text-navy-900">
                                <a href="{{ route('clubs.show', $club) }}" class="hover:text-accent">{{ $club->name }}</a>
                            </h2>
                            <p class="mt-1 text-xs text-muted">{{ $club->city }} · {{ number_format($club->member_count) }} عضو</p>
                        </div>
                        <div class="flex flex-wrap gap-1.5">
                            @if ($club->is_premium)<x-badge tone="premium">همکار ویژه</x-badge>@endif
                            @if ($club->is_verified)<x-badge tone="verified">تأییدشده</x-badge>@endif
                        </div>
                    </div>
                    <p class="mt-3 text-sm leading-7 text-muted">{{ $club->summary }}</p>
                    <a href="{{ route('clubs.show', $club) }}" class="mt-4 inline-flex rounded-full bg-accent px-4 py-2 text-xs font-bold text-navy-950">مشاهده پروفایل</a>
                </article>
            @endforeach
        </div>

        <div class="mt-8">{{ $clubs->links() }}</div>
    </section>
</x-layouts.site>
