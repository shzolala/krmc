@props([
    'tone' => 'neutral',
])

@php
    $classes = match ($tone) {
        'premium' => 'bg-accent/15 text-navy-900 ring-accent/30',
        'featured' => 'bg-sky-50 text-sky-800 ring-sky-200',
        'verified' => 'bg-emerald-50 text-emerald-800 ring-emerald-200',
        'leader' => 'bg-violet-50 text-violet-800 ring-violet-200',
        default => 'bg-slate-50 text-slate-700 ring-slate-200',
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center rounded-full px-2.5 py-1 text-[11px] font-semibold ring-1 ring-inset {$classes}"]) }}>
    {{ $slot }}
</span>
