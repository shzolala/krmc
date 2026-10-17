@php
    $categories = collect(\App\Enums\DestinationCategory::cases())->mapWithKeys(fn ($case) => [$case->value => $case->label()]);
    $baseQuery = array_filter(['q' => $search, 'region' => $activeRegion]);
@endphp

<x-layouts.site title="مقاصد" meta-description="قله‌ها و نقاط طبیعی استان کرمان با منطقه، ارتفاع و جاذبه‌های نزدیک.">
    <x-site.page-hero
        section="destinations"
        overlap
        :crumbs="['مقاصد' => null]"
        eyebrow="قله‌ها و نقاط طبیعی"
        title="مقصد بعدی‌تان را پیدا کنید"
        :description="fa_num($destinations->total()).' مقصد — هر کدام به منطقه کوهستانی خودش وصل است و جاذبه‌های نزدیک را هم نشان می‌دهد.'"
    />

    <x-site.toolbar
        :action="route('destinations.index')"
        placeholder="نام قله یا نقطه طبیعی..."
        :query="$search"
        :hidden="['category' => $activeCategory, 'region' => $activeRegion]"
        :total="$destinations->total()"
        :filters="collect(['' => 'همه'])->union($categories)->map(fn ($label, $value) => [
            'label' => $label,
            'href' => route('destinations.index', array_filter([...$baseQuery, 'category' => $value])),
            'active' => (string) $activeCategory === (string) $value,
        ])->values()->push(['label' => 'مناطق کوهستانی', 'href' => route('regions.index'), 'active' => false])->all()"
    />

    <x-site.section spacing="pt-10">
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($destinations as $destination)
                <x-cards.destination :destination="$destination" />
            @empty
                <x-site.empty class="col-span-full" message="مقصدی با این مشخصات پیدا نشد." :href="route('destinations.index')" />
            @endforelse
        </div>

        <div class="mt-10">{{ $destinations->links() }}</div>
    </x-site.section>

    @if (($mapMarkers ?? collect())->isNotEmpty())
        <x-site.section eyebrow="موقعیت جغرافیایی" title="مقاصد روی نقشه" :href="route('map')" link-label="نقشه کامل">
            <x-geo-map :markers="$mapMarkers" class="h-72" :zoom="7" />
        </x-site.section>
    @endif
</x-layouts.site>
