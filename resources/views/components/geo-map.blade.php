@props([
    'markers' => [],
    'height' => 'h-80',
    'zoom' => 8,
])

@php
    $markerList = collect($markers)->filter()->values()->all();
    $centerLat = $markerList[0]['lat'] ?? 30.2839;
    $centerLng = $markerList[0]['lng'] ?? 57.0834;
    $typeLabels = [
        'destination' => 'مقصد',
        'club' => 'باشگاه',
        'shop' => 'فروشگاه',
        'accommodation' => 'اقامتگاه',
        'shelter' => 'پناهگاه',
        'program' => 'برنامه',
    ];
@endphp

<div
    {{ $attributes->class(["overflow-hidden rounded-2xl ring-1 ring-navy-900/10 {$height}"]) }}
    data-geo-map
    data-center-lat="{{ $centerLat }}"
    data-center-lng="{{ $centerLng }}"
    data-zoom="{{ $zoom }}"
    data-markers='@json($markerList, JSON_UNESCAPED_UNICODE)'
    data-type-labels='@json($typeLabels, JSON_UNESCAPED_UNICODE)'
>
    <div data-geo-map-canvas dir="ltr" class="h-full w-full bg-sand"></div>
</div>

@once
    @push('head')
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
    @endpush

    @push('scripts')
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    @endpush
@endonce
