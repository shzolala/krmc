<x-layouts.site :title="$accommodation->name" :meta-description="$accommodation->summary">
    <x-site.detail-header
        :title="$accommodation->name"
        :crumbs="['اقامتگاه‌ها' => route('accommodations.index'), $accommodation->name => null]"
        :kicker="$accommodation->type->label()"
        :cover="$accommodation->cover_path ?: \App\Support\CatalogCover::path('cover-lodge.jpg')"
        :seed="'accommodation-'.$accommodation->slug"
        :reviewable="$accommodation"
    >
        <x-slot:badges>
            @if ($accommodation->is_verified)<x-badge tone="verified">تأییدشده</x-badge>@endif
        </x-slot:badges>
        <x-slot:meta>
            @if ($accommodation->city)
                <span class="inline-flex items-center gap-1.5"><x-site.icon name="pin" class="h-4 w-4 text-accent" />{{ $accommodation->city }}</span>
            @endif
            @if ($accommodation->destination)
                <a href="{{ route('destinations.show', $accommodation->destination) }}" class="inline-flex items-center gap-1.5 hover:text-accent">
                    <x-site.icon name="mountain" class="h-4 w-4 text-accent" />نزدیک {{ $accommodation->destination->name }}
                </a>
            @endif
        </x-slot:meta>
    </x-site.detail-header>

    <x-site.detail-layout>
        <x-site.panel title="درباره اقامتگاه" icon="home">
            <p class="whitespace-pre-line text-sm leading-8 text-muted">{{ $accommodation->description }}</p>
        </x-site.panel>

        <x-slot:aside>
            <x-site.panel title="اطلاعات" icon="document">
                <x-site.facts :columns="1" :items="[
                    ['label' => 'نوع', 'value' => $accommodation->type->label(), 'icon' => 'home'],
                    ['label' => 'شهر', 'value' => $accommodation->city, 'icon' => 'pin'],
                    ['label' => 'آدرس', 'value' => $accommodation->address, 'icon' => 'pin'],
                    ['label' => 'مقصد مرتبط', 'value' => $accommodation->destination?->name, 'icon' => 'mountain'],
                ]" />
            </x-site.panel>

            @if ($mapMarkers->isNotEmpty())
                <x-site.map-panel :markers="$mapMarkers" :zoom="12" />
            @else
                <x-site.panel><p class="text-sm text-muted">موقعیت جغرافیایی هنوز ثبت نشده است.</p></x-site.panel>
            @endif
        </x-slot:aside>
    </x-site.detail-layout>

    <x-site.section spacing="pt-14">
        <livewire:review.section :reviewable="$accommodation" :key="'accommodation-reviews-'.$accommodation->id" />
    </x-site.section>
</x-layouts.site>
