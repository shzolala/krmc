@props([
    'markers',
    'zoom' => 9,
    'title' => 'روی نقشه',
    'height' => 'h-56',
])

@if (collect($markers)->isNotEmpty())
    <x-site.panel :title="$title" icon="pin">
        <x-geo-map :markers="$markers" :zoom="$zoom" :height="$height" class="!rounded-2xl" />
        <a href="{{ route('map') }}" class="mt-3 inline-flex items-center gap-1.5 px-2 text-xs font-semibold text-accent transition hover:text-navy-900">
            باز کردن نقشه یکپارچه
            <x-site.icon name="arrow-left" class="h-3.5 w-3.5" />
        </a>
    </x-site.panel>
@endif
