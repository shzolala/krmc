@props([
    'items' => [],
    'columns' => 2,
])

@php
    $items = collect($items)->filter(fn (array $item) => filled($item['value'] ?? null))->values();
@endphp

@if ($items->isNotEmpty())
    <dl {{ $attributes->class(['grid gap-3', 'sm:grid-cols-2' => $columns >= 2, 'lg:grid-cols-3' => $columns >= 3, 'lg:grid-cols-4' => $columns >= 4]) }}>
        @foreach ($items as $item)
            <div class="flex items-start gap-3 rounded-2xl bg-sand px-4 py-3.5">
                @if (! empty($item['icon']))
                    <span class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-white text-accent shadow-sm">
                        <x-site.icon :name="$item['icon']" class="h-4 w-4" />
                    </span>
                @endif
                <div class="min-w-0">
                    <dt class="text-[11px] font-semibold text-muted">{{ $item['label'] }}</dt>
                    <dd class="mt-0.5 text-sm font-bold leading-6 text-navy-900">{{ $item['value'] }}</dd>
                </div>
            </div>
        @endforeach
    </dl>
@endif
