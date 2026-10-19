@props([
    'eyebrow' => null,
    'title',
    'description' => null,
    'href' => null,
    'linkLabel' => 'مشاهده همه',
    'tone' => 'light',
    'actions' => null,
    'link' => null,
])

@php
    $isDark = $tone === 'dark';
@endphp

<div {{ $attributes->class(['mb-5 flex flex-wrap justify-between gap-x-4 gap-y-3 sm:mb-7', 'items-end' => ! $actions, 'items-center' => (bool) $actions]) }}>
    <div class="max-w-2xl">
        @if ($eyebrow)
            <p @class(['text-[11px] font-bold tracking-wide', 'text-accent-soft' => $isDark, 'text-accent' => ! $isDark])>{{ $eyebrow }}</p>
        @endif
        <h2 @class(['text-xl font-extrabold leading-snug sm:text-2xl', 'mt-1' => $eyebrow, 'text-white' => $isDark, 'text-navy-900' => ! $isDark])>{{ $title }}</h2>
        @if ($description)
            <p @class(['mt-1 text-xs leading-6 sm:text-sm', 'text-white/65' => $isDark, 'text-muted' => ! $isDark])>{{ $description }}</p>
        @endif
    </div>

    @if ($actions)
        <div class="flex w-full items-center justify-between gap-3 lg:w-auto lg:justify-end lg:gap-6">
            {{ $actions }}
            @if ($link)
                {{ $link }}
            @endif
        </div>
    @elseif ($href)
        <a href="{{ $href }}" @class([
            'group inline-flex items-center gap-1.5 rounded-full px-3.5 py-1.5 text-xs font-semibold transition sm:text-sm',
            'bg-white/10 text-white hover:bg-white hover:text-navy-950' => $isDark,
            'bg-white text-navy-900 ring-1 ring-navy-900/10 hover:bg-navy-900 hover:text-white' => ! $isDark,
        ])>
            {{ $linkLabel }}
            <span aria-hidden="true" class="transition group-hover:-translate-x-1">←</span>
        </a>
    @endif
</div>
