<x-layouts.site title="مناطق کوهستانی" meta-description="مناطق کوهستانی استان کرمان؛ قله‌ها، نقاط طبیعی و جاذبه‌های نزدیک هر منطقه.">
    <x-site.page-hero
        section="regions"
        overlap
        :crumbs="['مناطق' => null]"
        eyebrow="جغرافیای کوهستان"
        title="مناطق کوهستانی کرمان"
        :description="fa_num($regions->total()).' منطقه — از توده‌های بلند تا بیابان و پارک ملی؛ هر منطقه قله‌ها و نقاط طبیعی خودش را دارد.'"
    />

    <x-site.toolbar
        :action="route('regions.index')"
        placeholder="نام منطقه..."
        :query="$search"
        :total="$regions->total()"
    />

    <x-site.section spacing="pt-10">
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($regions as $region)
                <x-cards.region :region="$region" />
            @empty
                <x-site.empty class="col-span-full" message="منطقه‌ای با این نام پیدا نشد." :href="route('regions.index')" />
            @endforelse
        </div>

        <div class="mt-10">{{ $regions->links() }}</div>
    </x-site.section>

    @if (($mapMarkers ?? collect())->isNotEmpty())
        <x-site.section eyebrow="موقعیت جغرافیایی" title="مناطق روی نقشه" :href="route('map')" link-label="نقشه کامل">
            <x-geo-map :markers="$mapMarkers" class="h-72" :zoom="7" />
        </x-site.section>
    @endif
</x-layouts.site>
