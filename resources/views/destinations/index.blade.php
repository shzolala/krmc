<x-layouts.site title="مقاصد" meta-description="قله‌ها و نقاط طبیعی استان کرمان با منطقه، ارتفاع و جاذبه‌های نزدیک.">
    <x-site.listing
        title="مقاصد کوهنوردی"
        :action="route('destinations.index')"
        :paginator="$destinations"
        empty-message="مقصدی با این مشخصات پیدا نشد."
        :filters="[
            ['type' => 'search', 'name' => 'q', 'label' => 'نام قله یا نقطه طبیعی', 'placeholder' => 'مثلاً هزار...'],
            ['type' => 'options', 'name' => 'category', 'label' => 'نوع مقصد', 'options' => collect(\App\Enums\DestinationCategory::cases())->mapWithKeys(fn ($case) => [$case->value => $case->label()])->all()],
            ['type' => 'options', 'name' => 'difficulty', 'label' => 'سطح سختی', 'options' => collect(\App\Enums\DifficultyLevel::cases())->mapWithKeys(fn ($level) => [$level->value => $level->label()])->all()],
            ['type' => 'select', 'name' => 'region', 'label' => 'منطقه', 'all' => 'همه مناطق', 'options' => $regions->pluck('name', 'slug')->all()],
        ]"
    >
        @foreach ($destinations as $destination)
            <x-cards.destination :destination="$destination" />
        @endforeach
    </x-site.listing>

    @if (($mapMarkers ?? collect())->isNotEmpty())
        <x-site.section eyebrow="موقعیت جغرافیایی" title="مقاصد روی نقشه" :href="route('map')" link-label="نقشه کامل">
            <x-geo-map :markers="$mapMarkers" class="h-72" :zoom="7" />
        </x-site.section>
    @endif
</x-layouts.site>
