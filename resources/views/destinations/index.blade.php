<x-layouts.site title="مقاصد تخصصی">
    <section class="bg-navy-900 text-white">
        <div class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
            <p class="text-sm text-white/60">خانه / مقاصد</p>
            <h1 class="mt-3 text-4xl font-extrabold">مقاصد تخصصی کوهنوردی</h1>
            <p class="mt-3 max-w-2xl text-sm leading-8 text-white/75">{{ $destinations->total() }} مقصد — با مشخص بودن باشگاه‌ها و مربیانی که آن مسیر را می‌برند.</p>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        @if (($mapMarkers ?? collect())->isNotEmpty())
            <div class="mb-10">
                <x-geo-map :markers="$mapMarkers" class="h-96" :zoom="8" />
            </div>
        @endif

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($destinations as $destination)
                <a href="{{ route('destinations.show', $destination) }}" class="group relative min-h-64 overflow-hidden rounded-2xl">
                    <img src="{{ $destination->cover_path }}" alt="{{ $destination->name }}" class="absolute inset-0 h-full w-full object-cover transition duration-500 group-hover:scale-105">
                    <div class="absolute inset-0 bg-gradient-to-t from-navy-950/90 via-navy-950/20 to-transparent"></div>
                    <div class="absolute inset-x-0 bottom-0 p-5 text-white">
                        <p class="text-[11px] text-white/80">{{ $destination->guides_count }} راهنما · {{ $destination->elevation ? number_format($destination->elevation).' متر' : '' }}</p>
                        <p class="mt-1 text-2xl font-bold">{{ $destination->name }}</p>
                        <p class="mt-2 text-xs font-semibold text-accent-soft">جزئیات بیشتر</p>
                    </div>
                </a>
            @endforeach
        </div>
        <div class="mt-8">{{ $destinations->links() }}</div>
    </section>
</x-layouts.site>
