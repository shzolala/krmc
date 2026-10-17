@props([
    'title' => null,
    'icon' => null,
    'tone' => 'default',
    'id' => null,
])

<section @if ($id) id="{{ $id }}" @endif {{ $attributes->class([
    'scroll-mt-24 rounded-3xl p-5 sm:p-7',
    'bg-white ring-1 ring-navy-900/5' => $tone === 'default',
    'bg-amber-50/80 ring-1 ring-amber-900/10' => $tone === 'warm',
    'bg-navy-900 text-white' => $tone === 'dark',
]) }}>
    @if ($title)
        <h2 @class(['flex items-center gap-2.5 text-lg font-extrabold', 'text-navy-900' => $tone !== 'dark', 'text-white' => $tone === 'dark'])>
            @if ($icon)
                <span class="flex h-8 w-8 items-center justify-center rounded-full bg-accent/15 text-accent">
                    <x-site.icon :name="$icon" class="h-[18px] w-[18px]" />
                </span>
            @endif
            {{ $title }}
        </h2>
    @endif

    <div @class(['mt-4' => $title])>{{ $slot }}</div>
</section>
