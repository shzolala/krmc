@props([
    'title',
    'description' => null,
])

<div {{ $attributes->class(['grid gap-8 overflow-hidden rounded-3xl bg-gradient-to-l from-accent to-accent-soft p-8 text-navy-950 sm:p-12 lg:grid-cols-[1.4fr_1fr] lg:items-center']) }}>
    <div>
        <h2 class="text-2xl font-extrabold leading-snug sm:text-3xl">{{ $title }}</h2>
        @if ($description)
            <p class="mt-3 max-w-xl text-sm leading-8 text-navy-900/80 sm:text-base">{{ $description }}</p>
        @endif
    </div>
    <div class="flex flex-wrap gap-3 lg:justify-end">{{ $slot }}</div>
</div>
