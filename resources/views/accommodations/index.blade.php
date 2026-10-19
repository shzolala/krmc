<x-layouts.site title="اقامتگاه‌ها" meta-description="بوم‌گردی، هتل و مهمان‌سرا در مسیرها و مقاصد کوهستانی کرمان.">
    <x-site.listing
        title="اقامتگاه‌ها"
        :action="route('accommodations.index')"
        :paginator="$accommodations"
        empty-message="اقامتگاهی با این مشخصات پیدا نشد."
        :filters="[
            ['type' => 'search', 'name' => 'q', 'label' => 'نام اقامتگاه یا شهر', 'placeholder' => 'مثلاً بوم‌گردی...'],
            ['type' => 'options', 'name' => 'type', 'label' => 'نوع اقامتگاه', 'options' => collect(\App\Enums\LodgingType::cases())->mapWithKeys(fn ($case) => [$case->value => $case->label()])->all()],
            ['type' => 'select', 'name' => 'city', 'label' => 'شهر', 'all' => 'همه شهرها', 'options' => $cities->all()],
            ['type' => 'toggle', 'name' => 'verified', 'label' => 'فقط تأییدشده'],
        ]"
    >
        @foreach ($accommodations as $accommodation)
            <x-cards.accommodation :accommodation="$accommodation" />
        @endforeach
    </x-site.listing>

    @if ($mapMarkers->isNotEmpty())
        <x-site.section eyebrow="موقعیت جغرافیایی" title="اقامتگاه‌ها روی نقشه" :href="route('map')" link-label="نقشه کامل">
            <x-geo-map :markers="$mapMarkers" class="h-72" :zoom="9" />
        </x-site.section>
    @endif
</x-layouts.site>
