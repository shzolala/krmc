@props([
    'model' => null,
    'average' => null,
    'count' => 0,
    'href' => null,
    'size' => 'sm',
])

@php
    if ($model) {
        ['average' => $average, 'count' => $count] = $model->reviewSummary();
    }

    $filled = (int) round((float) $average);
    $starSize = $size === 'lg' ? 'h-5 w-5' : 'h-3.5 w-3.5';
    $tag = $href ? 'a' : 'span';
@endphp

<{{ $tag }} @if ($href) href="{{ $href }}" @endif {{ $attributes->class(['inline-flex items-center gap-1.5 text-xs', 'text-sm' => $size === 'lg', 'transition hover:text-accent' => $href]) }}>
    @if ($count > 0)
        <span class="flex items-center gap-0.5 text-accent" aria-hidden="true">
            @for ($star = 1; $star <= 5; $star++)
                <svg viewBox="0 0 24 24" class="{{ $starSize }} {{ $star <= $filled ? 'fill-current' : 'fill-navy-900/15' }}"><path d="M12 3l2.6 5.6 6.1.7-4.5 4.2 1.2 6L12 16.5 6.6 19.5l1.2-6L3.3 9.3l6.1-.7L12 3z"/></svg>
            @endfor
        </span>
        <b class="font-extrabold text-current">{{ fa_num($average, 1) }}</b>
        <span class="opacity-70">({{ fa_num($count) }} نظر)</span>
    @else
        <span class="opacity-70">هنوز نظری ثبت نشده</span>
    @endif
</{{ $tag }}>
