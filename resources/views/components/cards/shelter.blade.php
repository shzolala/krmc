@props([
    'shelter',
])

<x-site.cover-card
    :href="route('shelters.show', $shelter)"
    :reviewable="$shelter"
    :title="$shelter->name"
    :image="$shelter->cover_path"
    kicker="پناهگاه کوهستانی"
    :badge="$shelter->elevation ? fa_num($shelter->elevation).' متر' : null"
    :meta="$shelter->destination?->name ?? 'جزئیات پناهگاه'"
    tone="warm"
    {{ $attributes }}
/>
