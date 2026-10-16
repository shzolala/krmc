<x-layouts.site :title="$accommodation->name" :meta-description="$accommodation->summary">
    <section class="bg-navy-900 text-white">
        <div class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
            <p class="text-sm text-white/60">خانه / اقامتگاه‌ها / {{ $accommodation->name }}</p>
            <p class="mt-4 text-xs font-semibold tracking-wide text-accent-soft">{{ $accommodation->type->label() }}</p>
            <h1 class="mt-2 text-4xl font-extrabold">{{ $accommodation->name }}</h1>
            <p class="mt-3 max-w-2xl text-sm leading-8 text-white/75">{{ $accommodation->summary }}</p>
            @if ($accommodation->destination)
                <p class="mt-4 text-sm">
                    مقصد مرتبط:
                    <a href="{{ route('destinations.show', $accommodation->destination) }}" class="text-accent-soft hover:underline">{{ $accommodation->destination->name }}</a>
                </p>
            @endif
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="grid gap-8 lg:grid-cols-[1.2fr_0.8fr]">
            <div>
                <h2 class="text-xl font-bold text-navy-900">درباره اقامتگاه</h2>
                <p class="mt-4 whitespace-pre-line text-sm leading-8 text-muted">{{ $accommodation->description }}</p>
                @if ($accommodation->address)
                    <p class="mt-6 text-sm text-muted">آدرس: {{ $accommodation->address }}</p>
                @endif
            </div>
            <div>
                @if ($mapMarkers->isNotEmpty())
                    <x-geo-map :markers="$mapMarkers" class="h-80" :zoom="12" />
                @else
                    <div class="rounded-2xl bg-white p-6 text-sm text-muted ring-1 ring-navy-900/5">موقعیت جغرافیایی هنوز ثبت نشده است.</div>
                @endif
            </div>
        </div>

        <div class="mt-10">
            <livewire:review.section :reviewable="$accommodation" :key="'accommodation-reviews-'.$accommodation->id" />
        </div>
    </section>
</x-layouts.site>
