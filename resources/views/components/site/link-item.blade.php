@props([
    'href',
    'title',
    'subtitle' => null,
    'image' => null,
    'kicker' => null,
])

<a href="{{ $href }}" {{ $attributes->class(['group flex items-center gap-3 rounded-2xl bg-sand p-3 transition hover:bg-white hover:shadow-lg hover:shadow-navy-900/10 hover:ring-1 hover:ring-accent/40']) }}>
    @if ($image)
        <img src="{{ $image }}" alt="" loading="lazy" class="h-14 w-14 shrink-0 rounded-xl object-cover">
    @endif
    <span class="min-w-0 flex-1">
        @if ($kicker)
            <span class="block text-[11px] font-semibold text-accent">{{ $kicker }}</span>
        @endif
        <span class="block truncate text-sm font-bold text-navy-900">{{ $title }}</span>
        @if ($subtitle)
            <span class="mt-0.5 block truncate text-xs text-muted">{{ $subtitle }}</span>
        @endif
    </span>
    <x-site.icon name="arrow-left" class="h-4 w-4 shrink-0 text-muted transition group-hover:-translate-x-1 group-hover:text-accent" />
</a>
