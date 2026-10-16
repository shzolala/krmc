<x-layouts.site :title="$shelter->name" :meta-description="$shelter->summary">
    <section class="bg-amber-950 text-white">
        <div class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
            <p class="text-sm text-white/60">خانه / پناهگاه‌ها / {{ $shelter->name }}</p>
            <x-badge tone="premium" class="mt-4 !bg-accent/20 !text-accent-soft !ring-accent/40">پناهگاه — نه اقامتگاه</x-badge>
            <h1 class="mt-4 text-4xl font-extrabold">{{ $shelter->name }}</h1>
            <p class="mt-3 max-w-2xl text-sm leading-8 text-white/80">{{ $shelter->summary }}</p>
            <p class="mt-4 text-sm text-white/70">
                @if ($shelter->destination)
                    مقصد: <a href="{{ route('destinations.show', $shelter->destination) }}" class="text-accent-soft hover:underline">{{ $shelter->destination->name }}</a>
                @endif
                @if ($shelter->elevation)
                    · ارتفاع {{ number_format($shelter->elevation) }} متر
                @endif
                @if ($shelter->capacity)
                    · ظرفیت حدودی {{ $shelter->capacity }} نفر
                @endif
            </p>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="grid gap-8 lg:grid-cols-[1.2fr_0.8fr]">
            <div class="rounded-3xl bg-amber-50/70 p-6 ring-1 ring-amber-900/10">
                <h2 class="text-xl font-bold text-navy-900">راهنمای پناهگاه</h2>
                <p class="mt-4 whitespace-pre-line text-sm leading-8 text-muted">{{ $shelter->description }}</p>
            </div>
            <div>
                @if ($mapMarkers->isNotEmpty())
                    <x-geo-map :markers="$mapMarkers" class="h-80" :zoom="12" />
                @endif
            </div>
        </div>

        <div class="mt-10">
            <livewire:review.section :reviewable="$shelter" :key="'shelter-reviews-'.$shelter->id" />
        </div>
    </section>
</x-layouts.site>
