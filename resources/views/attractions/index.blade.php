<x-layouts.site title="جاذبه‌های گردشگری" meta-description="جاذبه‌های تاریخی و گردشگری نزدیک مناطق کوهستانی استان کرمان.">
    <x-site.listing
        title="جاذبه‌های دیدنی کرمان"
        :action="route('attractions.index')"
        :paginator="$attractions"
        empty-message="جاذبه‌ای با این مشخصات پیدا نشد."
        :filters="[
            ['type' => 'search', 'name' => 'q', 'label' => 'نام جاذبه یا شهر', 'placeholder' => 'مثلاً باغ شاهزاده...'],
            ['type' => 'options', 'name' => 'type', 'label' => 'نوع جاذبه', 'options' => collect(\App\Enums\AttractionType::cases())->mapWithKeys(fn ($case) => [$case->value => $case->label()])->all()],
            ['type' => 'select', 'name' => 'region', 'label' => 'منطقه', 'all' => 'همه مناطق', 'options' => $regions->all()],
        ]"
    >
        @foreach ($attractions as $attraction)
            <x-cards.attraction :attraction="$attraction" />
        @endforeach
    </x-site.listing>

    @if (($mapMarkers ?? collect())->isNotEmpty())
        <x-site.section eyebrow="موقعیت جغرافیایی" title="جاذبه‌ها روی نقشه" :href="route('map')" link-label="نقشه کامل">
            <x-geo-map :markers="$mapMarkers" class="h-72" :zoom="7" />
        </x-site.section>
    @endif
</x-layouts.site>
