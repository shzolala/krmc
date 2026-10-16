<x-layouts.site title="اقامتگاه‌ها">
    <section class="bg-navy-900 text-white">
        <div class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
            <p class="text-sm text-white/60">خانه / اقامتگاه‌ها</p>
            <h1 class="mt-3 text-4xl font-extrabold">اقامتگاه‌ها</h1>
            <p class="mt-3 max-w-2xl text-sm leading-8 text-white/75">
                خانه‌های بوم‌گردی، هتل‌ها و سایر اقامتگاه‌ها — هر کدام با موقعیت جغرافیایی روی نقشه.
            </p>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        @if ($mapMarkers->isNotEmpty())
            <div class="mb-10">
                <x-geo-map :markers="$mapMarkers" class="h-96" :zoom="9" />
            </div>
        @endif

        <div class="grid gap-4 md:grid-cols-2">
            @foreach ($accommodations as $accommodation)
                <article class="overflow-hidden rounded-2xl border border-navy-900/8 bg-white">
                    @if ($accommodation->cover_path)
                        <img src="{{ $accommodation->cover_path }}" alt="" class="h-44 w-full object-cover">
                    @endif
                    <div class="p-5">
                        <div class="flex flex-wrap items-start justify-between gap-2">
                            <div>
                                <p class="text-[11px] font-semibold text-accent">{{ $accommodation->type->label() }}</p>
                                <h2 class="mt-1 text-lg font-bold text-navy-900">
                                    <a href="{{ route('accommodations.show', $accommodation) }}" class="hover:text-accent">{{ $accommodation->name }}</a>
                                </h2>
                            </div>
                            @if ($accommodation->is_verified)
                                <x-badge tone="verified">تأییدشده</x-badge>
                            @endif
                        </div>
                        <p class="mt-3 line-clamp-2 text-sm leading-7 text-muted">{{ $accommodation->summary }}</p>
                        <p class="mt-3 text-xs text-muted">
                            {{ $accommodation->city }}
                            @if ($accommodation->destination)
                                · نزدیک {{ $accommodation->destination->name }}
                            @endif
                            @if ($accommodation->hasCoordinates())
                                · روی نقشه
                            @endif
                        </p>
                    </div>
                </article>
            @endforeach
        </div>

        <div class="mt-8">{{ $accommodations->links() }}</div>
    </section>
</x-layouts.site>
