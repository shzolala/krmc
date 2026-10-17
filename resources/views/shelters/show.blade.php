<x-layouts.site :title="$shelter->name" :meta-description="$shelter->summary">
    <x-site.detail-header
        :title="$shelter->name"
        :crumbs="['پناهگاه‌ها' => route('shelters.index'), $shelter->name => null]"
        kicker="پناهگاه کوهستانی — نه اقامتگاه تجاری"
        :cover="$shelter->cover_path"
        :seed="'shelter-'.$shelter->slug"
        :reviewable="$shelter"
        :facts="[
            ['value' => $shelter->elevation ? fa_num($shelter->elevation).' متر' : null, 'label' => 'ارتفاع'],
            ['value' => $shelter->capacity ? fa_num($shelter->capacity).' نفر' : null, 'label' => 'ظرفیت حدودی'],
        ]"
    >
        <x-slot:meta>
            @if ($shelter->destination)
                <a href="{{ route('destinations.show', $shelter->destination) }}" class="inline-flex items-center gap-1.5 hover:text-accent">
                    <x-site.icon name="mountain" class="h-4 w-4 text-accent" />{{ $shelter->destination->name }}
                </a>
            @endif
        </x-slot:meta>
    </x-site.detail-header>

    <x-site.detail-layout>
        <x-site.panel title="راهنمای پناهگاه" icon="shelter" tone="warm">
            <p class="whitespace-pre-line text-sm leading-8 text-muted">{{ $shelter->description }}</p>
        </x-site.panel>

        <x-slot:aside>
            <x-site.map-panel :markers="$mapMarkers" :zoom="12" />

            <x-site.panel title="اطلاعات" icon="document">
                <x-site.facts :columns="1" :items="[
                    ['label' => 'مقصد', 'value' => $shelter->destination?->name],
                    ['label' => 'ارتفاع', 'value' => $shelter->elevation ? fa_num($shelter->elevation).' متر' : null],
                    ['label' => 'آدرس', 'value' => $shelter->address],
                ]" />
            </x-site.panel>
        </x-slot:aside>
    </x-site.detail-layout>

    <x-site.section spacing="pt-14">
        <livewire:review.section :reviewable="$shelter" :key="'shelter-reviews-'.$shelter->id" />
    </x-site.section>
</x-layouts.site>
