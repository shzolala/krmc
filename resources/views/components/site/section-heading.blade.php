@props([
    'eyebrow' => null,
    'title',
    'description' => null,
    'href' => null,
    'linkLabel' => 'مشاهده همه',
    'tone' => 'light',
])

@php
    $isDark = $tone === 'dark';
@endphp

<div {{ $attributes->class(['mb-8 flex flex-wrap items-end justify-between gap-4 sm:mb-10']) }}>
    <div class="max-w-2xl">
        @if ($eyebrow)
            <p @class(['text-xs font-bold tracking-wide', 'text-accent-soft' => $isDark, 'text-accent' => ! $isDark])>{{ $eyebrow }}</p>
        @endif
        <h2 @class(['mt-2 text-2xl font-extrabold leading-snug sm:text-3xl', 'text-white' => $isDark, 'text-navy-900' => ! $isDark])>{{ $title }}</h2>
        @if ($description)
            <p @class(['mt-3 text-sm leading-7 sm:text-base', 'text-white/70' => $isDark, 'text-muted' => ! $isDark])>{{ $description }}</p>
        @endif
    </div>

    @if ($href)
        <a href="{{ $href }}" @class([
            'group inline-flex items-center gap-2 rounded-full px-4 py-2 text-sm font-semibold transition',
            'bg-white/10 text-white hover:bg-white hover:text-navy-950' => $isDark,
            'bg-white text-navy-900 ring-1 ring-navy-900/10 hover:bg-navy-900 hover:text-white' => ! $isDark,
        ])>
            {{ $linkLabel }}
            <span aria-hidden="true" class="transition group-hover:-translate-x-1">←</span>
        </a>
    @endif
</div>
