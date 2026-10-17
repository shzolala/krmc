@props([
    'title',
    'href' => null,
    'image' => null,
    'kicker' => null,
    'summary' => null,
    'corner' => null,
    'badges' => null,
    'footer' => null,
    'imageHeight' => 'h-44',
    'reviewable' => null,
])

<article {{ $attributes->class(['group relative flex flex-col overflow-hidden rounded-3xl bg-white ring-1 ring-navy-900/5 transition hover:-translate-y-1 hover:shadow-xl hover:shadow-navy-900/10']) }}>
    @if ($image)
        <div @class(['relative overflow-hidden bg-navy-900', $imageHeight])>
            <img src="{{ $image }}" alt="" loading="lazy" class="h-full w-full object-cover transition duration-700 group-hover:scale-105">
            <div class="absolute inset-0 bg-gradient-to-t from-navy-950/55 to-transparent"></div>
            @if ($badges || $corner)
                <div class="absolute inset-x-3 top-3 flex items-start justify-between gap-2">
                    <div class="flex flex-wrap gap-1.5">{{ $badges }}</div>
                    @if ($corner)
                        <span class="rounded-full bg-white/90 px-2.5 py-0.5 text-[11px] font-medium text-navy-900">{{ $corner }}</span>
                    @endif
                </div>
            @endif
        </div>
    @endif

    <div class="flex flex-1 flex-col p-5">
        @if ($kicker)
            <p class="text-xs font-semibold text-accent">{{ $kicker }}</p>
        @endif
        <h3 @class(['font-bold leading-7 text-navy-900', 'mt-1' => $kicker])>
            @if ($href)
                <a href="{{ $href }}" class="after:absolute after:inset-0 focus:outline-none focus-visible:after:rounded-3xl focus-visible:after:ring-2 focus-visible:after:ring-accent">{{ $title }}</a>
            @else
                {{ $title }}
            @endif
        </h3>
        @if ($reviewable)
            <x-site.rating :model="$reviewable" class="mt-1.5 text-navy-900" />
        @endif
        @if ($summary)
            <p class="mt-2 line-clamp-2 text-xs leading-6 text-muted">{{ $summary }}</p>
        @endif

        {{ $slot }}

        @if ($footer)
            <div class="mt-auto flex items-center justify-between gap-3 border-t border-navy-900/5 pt-4 text-xs text-muted">{{ $footer }}</div>
        @endif
    </div>
</article>
