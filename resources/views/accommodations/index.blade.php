@php
    $types = collect(\App\Enums\LodgingType::cases())->mapWithKeys(fn ($case) => [$case->value => $case->label()]);
@endphp

<x-layouts.site title="اقامتگاه‌ها" meta-description="بوم‌گردی، هتل و مهمان‌سرا در مسیرها و مقاصد کوهستانی کرمان.">
    <x-site.page-hero
        section="accommodations"
        overlap
        :crumbs="['اقامتگاه‌ها' => null]"
        eyebrow="اقامت"
        title="اقامتگاه‌ها"
        description="خانه‌های بوم‌گردی، هتل‌ها و سایر اقامتگاه‌ها — هر کدام با موقعیت جغرافیایی روی نقشه."
    />

    <x-site.toolbar
        :action="route('accommodations.index')"
        placeholder="نام اقامتگاه یا شهر..."
        :query="$search"
        :hidden="['type' => $activeType]"
        :total="$accommodations->total()"
        :filters="collect(['' => 'همه'])->union($types)->map(fn ($label, $type) => [
            'label' => $label,
            'href' => route('accommodations.index', array_filter(['q' => $search, 'type' => $type])),
            'active' => (string) $activeType === (string) $type,
        ])->values()->all()"
    />

    <x-site.section spacing="pt-10">
        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
            @forelse ($accommodations as $accommodation)
                <x-cards.accommodation :accommodation="$accommodation" />
            @empty
                <x-site.empty class="col-span-full" message="اقامتگاهی با این مشخصات پیدا نشد." :href="route('accommodations.index')" />
            @endforelse
        </div>

        <div class="mt-10">{{ $accommodations->links() }}</div>
    </x-site.section>

    @if ($mapMarkers->isNotEmpty())
        <x-site.section eyebrow="موقعیت جغرافیایی" title="اقامتگاه‌ها روی نقشه" :href="route('map')" link-label="نقشه کامل">
            <x-geo-map :markers="$mapMarkers" class="h-72" :zoom="9" />
        </x-site.section>
    @endif
</x-layouts.site>
