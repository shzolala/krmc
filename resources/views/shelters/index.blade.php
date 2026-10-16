<x-layouts.site title="پناهگاه‌های کرمان">
    <section class="relative overflow-hidden bg-amber-950 text-white">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,rgba(212,160,23,0.25),transparent_50%)]"></div>
        <div class="relative mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
            <p class="text-xs font-semibold tracking-[0.25em] text-accent-soft">ویژه کوهنوردان</p>
            <h1 class="mt-3 text-4xl font-extrabold">پناهگاه‌های کرمان</h1>
            <p class="mt-3 max-w-2xl text-sm leading-8 text-white/80">
                پناهگاه‌ها اقامتگاه تجاری نیستند؛ نقاط حمایتی مسیرهای کوهستانی مانند هزار و جوپار که جدا و ویژه نمایش داده می‌شوند.
            </p>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        @if ($mapMarkers->isNotEmpty())
            <div class="mb-10">
                <x-geo-map :markers="$mapMarkers" class="h-96" :zoom="9" />
            </div>
        @endif

        <div class="grid gap-5 md:grid-cols-2">
            @foreach ($shelters as $shelter)
                <a href="{{ route('shelters.show', $shelter) }}" class="group relative min-h-64 overflow-hidden rounded-3xl ring-2 ring-amber-700/30">
                    <img src="{{ $shelter->cover_path }}" alt="{{ $shelter->name }}" class="absolute inset-0 h-full w-full object-cover transition duration-500 group-hover:scale-105">
                    <div class="absolute inset-0 bg-gradient-to-t from-amber-950 via-amber-950/40 to-transparent"></div>
                    <div class="absolute inset-x-0 bottom-0 p-6 text-white">
                        <p class="text-[11px] font-semibold tracking-wide text-accent-soft">پناهگاه کوهستانی</p>
                        <h2 class="mt-1 text-2xl font-extrabold">{{ $shelter->name }}</h2>
                        <p class="mt-2 text-sm text-white/80">
                            @if ($shelter->destination){{ $shelter->destination->name }} · @endif
                            @if ($shelter->elevation)ارتفاع {{ number_format($shelter->elevation) }} متر@endif
                        </p>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="mt-8">{{ $shelters->links() }}</div>
    </section>
</x-layouts.site>
