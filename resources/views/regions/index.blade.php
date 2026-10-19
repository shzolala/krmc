<x-layouts.site title="مناطق کوهستانی" meta-description="مناطق کوهستانی استان کرمان؛ قله‌ها، نقاط طبیعی و جاذبه‌های نزدیک هر منطقه.">
    <x-site.listing
        title="مناطق کوهستانی کرمان"
        :action="route('regions.index')"
        :paginator="$regions"
        empty-message="منطقه‌ای با این مشخصات پیدا نشد."
        :filters="[
            ['type' => 'search', 'name' => 'q', 'label' => 'نام منطقه', 'placeholder' => 'مثلاً لوت...'],
            ['type' => 'select', 'name' => 'type', 'label' => 'نوع منطقه', 'all' => 'همه انواع', 'options' => collect(\App\Enums\MountainRegionType::cases())->mapWithKeys(fn ($case) => [$case->value => $case->label()])->all()],
        ]"
    >
        @foreach ($regions as $region)
            <x-cards.region :region="$region" />
        @endforeach
    </x-site.listing>

    @if (($mapMarkers ?? collect())->isNotEmpty())
        <x-site.section eyebrow="موقعیت جغرافیایی" title="مناطق روی نقشه" :href="route('map')" link-label="نقشه کامل">
            <x-geo-map :markers="$mapMarkers" class="h-72" :zoom="7" />
        </x-site.section>
    @endif
</x-layouts.site>
