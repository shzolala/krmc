@props([
    'report',
])

<x-site.media-card
    :href="null"
    :title="$report->title"
    :image="$report->cover_path"
    :kicker="collect([$report->destination?->name, $report->club?->name])->filter()->implode(' · ')"
    :summary="$report->excerpt"
    {{ $attributes }}
>
    @if ($report->published_at)
        <x-slot:footer>
            <span>{{ fa_digits($report->published_at->format('Y/m/d')) }}</span>
        </x-slot:footer>
    @endif
</x-site.media-card>
