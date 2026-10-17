@props([
    'destination',
    'featured' => false,
])

<x-site.cover-card
    :href="route('destinations.show', $destination)"
    :reviewable="$destination"
    :title="$destination->name"
    :image="$destination->cover_path"
    :kicker="$destination->mountainRegion?->name ?? $destination->categoryLabel()"
    :badge="$destination->elevation ? fa_num($destination->elevation).' متر' : null"
    :summary="$destination->summary"
    :featured="$featured"
    :meta="isset($destination->guides_count) ? fa_num($destination->guides_count).' راهنما' : $destination->categoryLabel()"
    {{ $attributes }}
/>
