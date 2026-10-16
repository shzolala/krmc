<x-layouts.site :title="$club->name" :meta-description="$club->summary">
    <section class="bg-navy-900 text-white">
        <div class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
            <p class="text-sm text-white/60">خانه / باشگاه‌ها / {{ $club->name }}</p>
            <div class="mt-4 flex flex-wrap items-center gap-2">
                @if ($club->is_premium)<x-badge tone="premium">همکار ویژه</x-badge>@endif
                @if ($club->is_featured)<x-badge tone="featured">ویژه</x-badge>@endif
                @if ($club->is_verified)<x-badge tone="verified">تأییدشده</x-badge>@endif
            </div>
            <h1 class="mt-4 text-4xl font-extrabold">{{ $club->name }}</h1>
            <p class="mt-3 max-w-2xl text-sm leading-8 text-white/75">{{ $club->summary }}</p>
            <p class="mt-6 text-sm text-white/70">{{ number_format($club->member_count) }} عضو · {{ number_format($club->program_count) }} برنامه</p>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="grid gap-10 lg:grid-cols-[1.4fr_0.8fr]">
            <div>
                <h2 class="text-xl font-bold text-navy-900">درباره باشگاه</h2>
                <p class="mt-4 whitespace-pre-line text-sm leading-8 text-muted">{{ $club->description }}</p>

                @if ($club->coachProfiles->isNotEmpty())
                    <h3 class="mt-10 text-lg font-bold text-navy-900">مربیان و سرپرستان</h3>
                    <ul class="mt-4 space-y-3">
                        @foreach ($club->coachProfiles as $coach)
                            <li class="rounded-xl bg-white p-4 ring-1 ring-navy-900/5">
                                <a href="{{ route('coaches.show', $coach) }}" class="font-semibold text-navy-900 hover:text-accent">{{ $coach->user->name }}</a>
                                <p class="text-xs text-muted">{{ $coach->roleLabel() }} · {{ $coach->specialty }}</p>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            <aside class="rounded-2xl bg-white p-6 ring-1 ring-navy-900/5">
                <h3 class="font-bold text-navy-900">گزارش برنامه‌ها</h3>
                <ul class="mt-4 space-y-3">
                    @forelse ($club->programReports as $report)
                        <li class="text-sm">
                            <p class="font-medium text-navy-900">{{ $report->title }}</p>
                            <p class="text-xs text-muted">{{ $report->published_at?->format('Y/m/d') }}</p>
                        </li>
                    @empty
                        <li class="text-sm text-muted">هنوز گزارشی ثبت نشده است.</li>
                    @endforelse
                </ul>
            </aside>
        </div>

        <div class="mt-10">
            <livewire:review.section :reviewable="$club" :key="'club-reviews-'.$club->id" />
        </div>
    </section>
</x-layouts.site>
