@props([
    'attraction',
])

<x-site.media-card
    :href="route('attractions.show', $attraction)"
    :reviewable="$attraction"
    :title="$attraction->name"
    :image="$attraction->cover_path"
    :kicker="$attraction->mountainRegion?->name ?? $attraction->city"
    :summary="$attraction->summary"
    :corner="$attraction->isUnesco() ? 'یونسکو' : $attraction->typeLabel()"
    {{ $attributes }}
>
    @isset($attraction->destinations_count)
        <x-slot:footer>
            <span>{{ $attraction->city ?? 'کرمان' }}</span>
            <span><b class="text-navy-900">{{ fa_num($attraction->destinations_count) }}</b> مقصد نزدیک</span>
        </x-slot:footer>
    @endisset
</x-site.media-card>
