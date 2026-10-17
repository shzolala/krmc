@props([
    'href',
    'title',
    'image',
    'kicker' => null,
    'meta' => null,
    'summary' => null,
    'badge' => null,
    'featured' => false,
    'reviewable' => null,
    'tone' => 'navy',
])

<a href="{{ $href }}" {{ $attributes->class([
    'group relative isolate block min-h-64 overflow-hidden rounded-3xl',
    'bg-navy-900' => $tone === 'navy',
    'bg-amber-950 ring-2 ring-amber-700/30' => $tone === 'warm',
    'md:min-h-[32rem]' => $featured,
]) }}>
    <img src="{{ $image }}" alt="{{ $title }}" loading="lazy" class="absolute inset-0 -z-10 h-full w-full object-cover transition duration-700 group-hover:scale-105">
    <div @class(['absolute inset-0 -z-10 bg-gradient-to-t to-transparent', 'from-navy-950/90 via-navy-950/20' => $tone === 'navy', 'from-amber-950 via-amber-950/40' => $tone === 'warm'])></div>

    @if ($badge)
        <span class="absolute right-4 top-4 rounded-full bg-white/90 px-3 py-1 text-xs font-bold text-navy-900 backdrop-blur">{{ $badge }}</span>
    @endif

    <div class="absolute inset-x-0 bottom-0 p-5 text-white sm:p-6">
        @if ($kicker)
            <p class="text-xs font-semibold text-accent-soft">{{ $kicker }}</p>
        @endif
        <p @class(['mt-1 font-extrabold', 'text-3xl sm:text-4xl' => $featured, 'text-xl' => ! $featured])>{{ $title }}</p>
        @if ($featured && $summary)
            <p class="mt-3 line-clamp-2 max-w-md text-sm leading-7 text-white/80">{{ $summary }}</p>
        @endif
        @if ($reviewable)
            <x-site.rating :model="$reviewable" class="mt-2 text-white" />
        @endif
        @if ($meta)
            <p class="mt-3 inline-flex items-center gap-1.5 text-xs font-semibold text-white/90">
                {{ $meta }}
                <x-site.icon name="arrow-left" class="h-3.5 w-3.5 transition group-hover:-translate-x-1" />
            </p>
        @endif
    </div>
</a>
