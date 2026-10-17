@php
    $types = collect(\App\Enums\AttractionType::cases())->mapWithKeys(fn ($case) => [$case->value => $case->label()]);
@endphp

<x-layouts.site title="جاذبه‌های گردشگری" meta-description="جاذبه‌های تاریخی و گردشگری نزدیک مناطق کوهستانی استان کرمان.">
    <x-site.page-hero
        section="attractions"
        overlap
        :crumbs="['جاذبه‌ها' => null]"
        eyebrow="گردشگری"
        title="جاذبه‌های دیدنی کرمان"
        :description="fa_num($attractions->total()).' جاذبه — ببینید کنار قله‌ها و مسیرهای کوهنوردی چه مکان‌های تاریخی و گردشگری دیگری هست.'"
    />

    <x-site.toolbar
        :action="route('attractions.index')"
        placeholder="نام جاذبه یا شهر..."
        :query="$search"
        :hidden="['type' => $activeType]"
        :total="$attractions->total()"
        :filters="collect(['' => 'همه'])->union($types)->map(fn ($label, $type) => [
            'label' => $label,
            'href' => route('attractions.index', array_filter(['q' => $search, 'type' => $type])),
            'active' => (string) $activeType === (string) $type,
        ])->values()->all()"
    />

    <x-site.section spacing="pt-10">
        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
            @forelse ($attractions as $attraction)
                <x-cards.attraction :attraction="$attraction" />
            @empty
                <x-site.empty class="col-span-full" message="جاذبه‌ای با این مشخصات پیدا نشد." :href="route('attractions.index')" />
            @endforelse
        </div>

        <div class="mt-10">{{ $attractions->links() }}</div>
    </x-site.section>

    @if (($mapMarkers ?? collect())->isNotEmpty())
        <x-site.section eyebrow="موقعیت جغرافیایی" title="جاذبه‌ها روی نقشه" :href="route('map')" link-label="نقشه کامل">
            <x-geo-map :markers="$mapMarkers" class="h-72" :zoom="7" />
        </x-site.section>
    @endif
</x-layouts.site>
