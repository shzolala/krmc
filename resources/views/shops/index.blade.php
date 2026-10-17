<x-layouts.site title="فروشگاه‌ها" meta-description="فروشگاه‌های تجهیزات کوهنوردی مرتبط با جامعه کوهنوردان کرمان.">
    <x-site.page-hero
        section="shops"
        overlap
        :crumbs="['فروشگاه‌ها' => null]"
        eyebrow="تجهیزات"
        title="فروشگاه‌های تجهیزات"
        :description="fa_num($shops->total()).' فروشگاه مرتبط با جامعه کوهنوردی کرمان.'"
    />

    <x-site.toolbar
        :action="route('shops.index')"
        placeholder="نام فروشگاه یا شهر..."
        :query="$search"
        :total="$shops->total()"
    />

    <x-site.section spacing="pt-10">
        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
            @forelse ($shops as $shop)
                <x-cards.shop :shop="$shop" />
            @empty
                <x-site.empty class="col-span-full" message="فروشگاهی با این مشخصات پیدا نشد." :href="route('shops.index')" />
            @endforelse
        </div>

        <div class="mt-10">{{ $shops->links() }}</div>
    </x-site.section>
</x-layouts.site>
