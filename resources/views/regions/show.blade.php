<x-layouts.site :title="$region->name" :meta-description="$region->summary">
    <x-site.detail-header
        :title="$region->name"
        :crumbs="['مناطق' => route('regions.index'), $region->name => null]"
        :kicker="$region->typeLabel()"
        :cover="$region->cover_path"
        :seed="'region-'.$region->slug"
        :facts="[
            ['value' => $region->highest_elevation ? fa_num($region->highest_elevation).' متر' : null, 'label' => 'بلندترین نقطه'],
            ['value' => fa_num($peaks->count()), 'label' => 'قله'],
            ['value' => fa_num($natureSpots->count()), 'label' => 'نقطه طبیعی'],
            ['value' => fa_num($region->attractions->count()), 'label' => 'جاذبه نزدیک'],
        ]"
    >
        <x-slot:meta>
            @if ($region->nearest_city)
                <span class="inline-flex items-center gap-1.5"><x-site.icon name="pin" class="h-4 w-4 text-accent" />نزدیک {{ $region->nearest_city }}</span>
            @endif
        </x-slot:meta>
    </x-site.detail-header>

    <x-site.detail-layout>
        <x-site.panel title="درباره منطقه" icon="compass">
            <p class="whitespace-pre-line text-sm leading-8 text-muted">{{ $region->description }}</p>
        </x-site.panel>

        <x-slot:aside>
            <x-site.map-panel :markers="$mapMarkers" :zoom="9" />

            <x-site.panel title="موقعیت" icon="pin">
                <x-site.facts :columns="1" :items="[
                    ['label' => 'استان', 'value' => $region->province ?? 'کرمان'],
                    ['label' => 'شهرستان‌ها', 'value' => ! empty($region->counties) ? implode('، ', $region->counties) : null],
                    ['label' => 'نزدیک‌ترین شهر', 'value' => $region->nearest_city],
                ]" />
            </x-site.panel>
        </x-slot:aside>
    </x-site.detail-layout>

    @if ($peaks->isNotEmpty())
        <x-site.section id="peaks" eyebrow="صعود" title="قله‌های این منطقه" spacing="pt-14">
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($peaks as $peak)
                    <x-cards.destination :destination="$peak" />
                @endforeach
            </div>
        </x-site.section>
    @endif

    @if ($natureSpots->isNotEmpty())
        <x-site.section id="nature-spots" eyebrow="طبیعت‌گردی" title="نقاط طبیعی" spacing="pt-14">
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($natureSpots as $spot)
                    <x-cards.destination :destination="$spot" />
                @endforeach
            </div>
        </x-site.section>
    @endif

    <x-site.section id="attractions" eyebrow="گردشگری" title="جاذبه‌های نزدیک" spacing="pt-14">
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @forelse ($region->attractions as $attraction)
                <x-cards.attraction :attraction="$attraction" />
            @empty
                <x-site.empty class="col-span-full" message="هنوز جاذبه‌ای به این منطقه متصل نشده است." />
            @endforelse
        </div>
    </x-site.section>
</x-layouts.site>
