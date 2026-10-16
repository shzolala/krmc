<x-layouts.site :title="$destination->name" :meta-description="$destination->summary">
    <section class="bg-navy-900 text-white">
        <div class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
            <p class="text-sm text-white/60">خانه / مقاصد / {{ $destination->name }}</p>
            <h1 class="mt-3 text-4xl font-extrabold">{{ $destination->name }}</h1>
            <p class="mt-3 text-sm text-white/70">
                {{ $destination->region }}
                @if ($destination->elevation)
                    · ارتفاع {{ number_format($destination->elevation) }} متر
                @endif
                @if ($destination->hasCoordinates())
                    · روی نقشه
                @endif
            </p>
            <p class="mt-4 max-w-2xl text-sm leading-8 text-white/80">{{ $destination->summary }}</p>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        @if ($mapMarkers->isNotEmpty())
            <div class="mb-10">
                <x-geo-map :markers="$mapMarkers" class="h-96" :zoom="11" />
            </div>
        @endif

        <div class="grid gap-10 lg:grid-cols-[1.3fr_0.9fr]">
            <div>
                <h2 class="text-xl font-bold text-navy-900">درباره مقصد</h2>
                <p class="mt-4 whitespace-pre-line text-sm leading-8 text-muted">{{ $destination->description }}</p>

                @if ($destination->shelters->isNotEmpty())
                    <h3 class="mt-10 text-lg font-bold text-amber-900">پناهگاه‌های این مقصد</h3>
                    <ul class="mt-4 space-y-3">
                        @foreach ($destination->shelters as $shelter)
                            <li class="rounded-xl bg-amber-50 p-4 ring-1 ring-amber-900/10">
                                <a href="{{ route('shelters.show', $shelter) }}" class="font-semibold text-amber-950 hover:text-accent">{{ $shelter->name }}</a>
                                <p class="mt-1 text-xs text-muted">پناهگاه کوهستانی · نه اقامتگاه تجاری</p>
                            </li>
                        @endforeach
                    </ul>
                @endif

                @if ($destination->accommodations->isNotEmpty())
                    <h3 class="mt-10 text-lg font-bold text-navy-900">اقامتگاه‌های نزدیک</h3>
                    <ul class="mt-4 space-y-3">
                        @foreach ($destination->accommodations as $accommodation)
                            <li class="rounded-xl bg-white p-4 ring-1 ring-navy-900/5">
                                <a href="{{ route('accommodations.show', $accommodation) }}" class="font-semibold text-navy-900 hover:text-accent">{{ $accommodation->name }}</a>
                                <p class="mt-1 text-xs text-muted">{{ $accommodation->type->label() }}</p>
                            </li>
                        @endforeach
                    </ul>
                @endif

                @if ($destination->programReports->isNotEmpty())
                    <h3 class="mt-10 text-lg font-bold text-navy-900">گزارش برنامه‌ها</h3>
                    <ul class="mt-4 space-y-3">
                        @foreach ($destination->programReports as $report)
                            <li class="rounded-xl bg-white p-4 ring-1 ring-navy-900/5">
                                <p class="font-semibold text-navy-900">{{ $report->title }}</p>
                                <p class="mt-1 text-xs text-muted">{{ $report->excerpt }}</p>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            <aside>
                <h2 class="text-xl font-bold text-navy-900">چه کسانی این مقصد را می‌برند؟</h2>
                <div class="mt-4 space-y-3">
                    @forelse ($destination->guides as $guide)
                        <div class="rounded-xl bg-white p-4 ring-1 ring-navy-900/5">
                            @if ($guide->club)
                                <p class="text-[11px] font-semibold text-muted">باشگاه</p>
                                <a href="{{ route('clubs.show', $guide->club) }}" class="font-bold text-navy-900 hover:text-accent">{{ $guide->club->name }}</a>
                            @endif
                            @if ($guide->coachProfile)
                                <p class="text-[11px] font-semibold text-muted">{{ $guide->coachProfile->roleLabel() }}</p>
                                <a href="{{ route('coaches.show', $guide->coachProfile) }}" class="font-bold text-navy-900 hover:text-accent">{{ $guide->coachProfile->user->name }}</a>
                            @endif
                        </div>
                    @empty
                        <p class="text-sm text-muted">هنوز راهنمایی برای این مقصد ثبت نشده است.</p>
                    @endforelse
                </div>
            </aside>
        </div>

        <div class="mt-10">
            <livewire:review.section :reviewable="$destination" :key="'destination-reviews-'.$destination->id" />
        </div>
    </section>
</x-layouts.site>
