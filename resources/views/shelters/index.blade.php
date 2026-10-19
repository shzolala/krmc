<x-layouts.site title="پناهگاه‌های کرمان" meta-description="پناهگاه‌های کوهستانی کرمان؛ نقاط حمایتی مسیرها، جدا از اقامتگاه‌های تجاری.">
    <x-site.listing
        title="پناهگاه‌های کرمان"
        :action="route('shelters.index')"
        :paginator="$shelters"
        grid="md:grid-cols-2 xl:grid-cols-3"
        empty-message="پناهگاهی با این مشخصات پیدا نشد."
        :filters="[
            ['type' => 'search', 'name' => 'q', 'label' => 'نام پناهگاه', 'placeholder' => 'مثلاً هزار...'],
            ['type' => 'select', 'name' => 'destination', 'label' => 'مقصد (قله)', 'all' => 'همه مقاصد', 'options' => $destinations->all()],
        ]"
    >
        @foreach ($shelters as $shelter)
            <x-cards.shelter :shelter="$shelter" />
        @endforeach
    </x-site.listing>

    @if ($mapMarkers->isNotEmpty())
        <x-site.section eyebrow="موقعیت جغرافیایی" title="پناهگاه‌ها روی نقشه" :href="route('map')" link-label="نقشه کامل">
            <x-geo-map :markers="$mapMarkers" class="h-72" :zoom="9" />
        </x-site.section>
    @endif
</x-layouts.site>
