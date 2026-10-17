@props([
    'name',
    'image' => null,
    'size' => 'md',
])

@php
    $sizeClasses = match ($size) {
        'xl' => 'h-24 w-24 text-3xl',
        'lg' => 'h-20 w-20 text-3xl',
        'sm' => 'h-10 w-10 text-sm',
        default => 'h-14 w-14 text-xl',
    };
@endphp

<span {{ $attributes->class(['flex shrink-0 items-center justify-center overflow-hidden rounded-full bg-navy-900 font-extrabold text-accent-soft', $sizeClasses]) }} aria-hidden="true">
    @if ($image)
        <img src="{{ $image }}" alt="" class="h-full w-full object-cover">
    @else
        {{ mb_substr($name, 0, 1) }}
    @endif
</span>
