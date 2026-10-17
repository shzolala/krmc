@props([
    'href' => null,
    'variant' => 'primary',
    'icon' => null,
])

@php
    $classes = match ($variant) {
        'secondary' => 'bg-white/10 text-white ring-1 ring-white/25 backdrop-blur hover:bg-white/20',
        'dark' => 'bg-navy-900 text-white hover:bg-navy-700',
        'light' => 'bg-white/60 text-navy-900 hover:bg-white',
        'outline' => 'bg-white text-navy-900 ring-1 ring-navy-900/10 hover:bg-navy-900 hover:text-white',
        default => 'bg-accent text-navy-950 shadow-lg shadow-black/10 hover:bg-accent-soft',
    };
@endphp

<a href="{{ $href }}" {{ $attributes->class(['group inline-flex items-center justify-center gap-2 rounded-full px-6 py-3 text-sm font-bold transition focus:outline-none focus-visible:ring-2 focus-visible:ring-accent focus-visible:ring-offset-2', $classes]) }}>
    @if ($icon)
        <x-site.icon :name="$icon" class="h-4 w-4" />
    @endif
    {{ $slot }}
</a>
