<x-layouts.site title="مربیان و سرپرستان">
    <section class="bg-navy-900 text-white">
        <div class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
            <p class="text-sm text-white/60">خانه / مربیان و سرپرستان</p>
            <h1 class="mt-3 text-4xl font-extrabold">مربیان و سرپرستان</h1>
            <p class="mt-3 max-w-2xl text-sm leading-8 text-white/75">{{ $coaches->total() }} پروفایل تخصصی با دسترسی و نشان‌های جداگانه.</p>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="grid gap-4 md:grid-cols-2">
            @foreach ($coaches as $coach)
                <article class="rounded-2xl border border-navy-900/8 bg-white p-5">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h2 class="text-lg font-bold text-navy-900">
                                <a href="{{ route('coaches.show', $coach) }}" class="hover:text-accent">{{ $coach->user->name }}</a>
                            </h2>
                            <p class="mt-1 text-xs text-muted">{{ $coach->roleLabel() }} · {{ $coach->specialty }}</p>
                        </div>
                        <div class="flex flex-wrap justify-end gap-1.5">
                            @if ($coach->is_leader)<x-badge tone="leader">سرپرست</x-badge>@endif
                            @if ($coach->is_verified)<x-badge tone="verified">تأییدشده</x-badge>@endif
                        </div>
                    </div>
                    <p class="mt-3 line-clamp-2 text-sm leading-7 text-muted">{{ $coach->bio }}</p>
                </article>
            @endforeach
        </div>
        <div class="mt-8">{{ $coaches->links() }}</div>
    </section>
</x-layouts.site>
