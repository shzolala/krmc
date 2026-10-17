@props([
    'aside' => null,
])

<div {{ $attributes->class(['mx-auto grid max-w-7xl gap-8 px-4 pt-6 sm:px-6 lg:grid-cols-[minmax(0,1fr)_22rem] lg:px-8 lg:pt-8']) }}>
    <div class="min-w-0 space-y-6">{{ $slot }}</div>

    @if ($aside)
        <aside class="min-w-0 space-y-6 lg:sticky lg:top-24 lg:self-start">{{ $aside }}</aside>
    @endif
</div>
