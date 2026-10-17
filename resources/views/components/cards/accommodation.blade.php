@props([
    'accommodation',
])

<x-site.media-card
    :href="route('accommodations.show', $accommodation)"
    :reviewable="$accommodation"
    :title="$accommodation->name"
    :image="$accommodation->cover_path ?: \App\Support\CatalogCover::path('cover-lodge.jpg')"
    :kicker="$accommodation->type->label()"
    :summary="$accommodation->summary"
    :corner="$accommodation->city"
    {{ $attributes }}
>
    @if ($accommodation->is_verified)
        <x-slot:badges>
            <span class="rounded-full bg-emerald-500 px-2.5 py-0.5 text-[11px] font-semibold text-white">تأییدشده</span>
        </x-slot:badges>
    @endif

    @if ($accommodation->destination)
        <x-slot:footer>
            <span class="inline-flex items-center gap-1.5">
                <x-site.icon name="pin" class="h-3.5 w-3.5 text-accent" />
                نزدیک {{ $accommodation->destination->name }}
            </span>
        </x-slot:footer>
    @endif
</x-site.media-card>
