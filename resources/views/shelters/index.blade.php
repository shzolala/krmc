<x-layouts.site title="پناهگاه‌های کرمان" meta-description="پناهگاه‌های کوهستانی کرمان؛ نقاط حمایتی مسیرها، جدا از اقامتگاه‌های تجاری.">
    <x-site.page-hero
        section="shelters"
        overlap
        :crumbs="['پناهگاه‌ها' => null]"
        eyebrow="ویژه کوهنوردان"
        title="پناهگاه‌های کرمان"
        description="پناهگاه‌ها اقامتگاه تجاری نیستند؛ نقاط حمایتی مسیرهای کوهستانی مانند هزار و جوپار که جدا و ویژه نمایش داده می‌شوند."
    />

    <x-site.toolbar
        :action="route('shelters.index')"
        placeholder="نام پناهگاه..."
        :query="$search"
        :total="$shelters->total()"
    />

    <x-site.section spacing="pt-10">
        <div class="grid gap-5 md:grid-cols-2">
            @forelse ($shelters as $shelter)
                <x-cards.shelter :shelter="$shelter" />
            @empty
                <x-site.empty class="col-span-full" message="پناهگاهی با این نام پیدا نشد." :href="route('shelters.index')" />
            @endforelse
        </div>

        <div class="mt-10">{{ $shelters->links() }}</div>
    </x-site.section>

    @if ($mapMarkers->isNotEmpty())
        <x-site.section eyebrow="موقعیت جغرافیایی" title="پناهگاه‌ها روی نقشه" :href="route('map')" link-label="نقشه کامل">
            <x-geo-map :markers="$mapMarkers" class="h-72" :zoom="9" />
        </x-site.section>
    @endif
</x-layouts.site>
