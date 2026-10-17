@props([
    'items' => [],
])

@php
    $items = collect($items)->filter(fn (array $item) => filled($item['value'] ?? null))->values();
@endphp

@if ($items->isNotEmpty())
    <dl {{ $attributes->class(['grid grid-cols-2 gap-px overflow-hidden rounded-2xl bg-white/15 ring-1 ring-white/20 backdrop-blur-md', 'sm:grid-cols-4' => $items->count() >= 4, 'sm:grid-cols-3' => $items->count() === 3]) }}>
        @foreach ($items as $item)
            @php($tag = ($item['href'] ?? null) ? 'a' : 'div')
            <{{ $tag }} @if ($tag === 'a') href="{{ $item['href'] }}" @endif class="group flex flex-col bg-navy-950/40 px-4 py-4 transition hover:bg-navy-950/20">
                <dd class="text-xl font-extrabold text-white transition group-hover:text-accent-soft sm:text-2xl">{{ $item['value'] }}</dd>
                <dt class="mt-1 text-xs text-white/70">{{ $item['label'] }}</dt>
            </{{ $tag }}>
        @endforeach
    </dl>
@endif
