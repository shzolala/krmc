@props([
    'region',
])

<x-site.cover-card
    :href="route('regions.show', $region)"
    :title="$region->name"
    :image="$region->cover_path"
    :kicker="$region->typeLabel()"
    :badge="$region->highest_elevation ? 'تا '.fa_num($region->highest_elevation).' متر' : null"
    :meta="fa_num($region->destinations_count).' مقصد · '.fa_num($region->attractions_count).' جاذبه'"
    {{ $attributes }}
/>
