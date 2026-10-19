<x-layouts.site title="فروشگاه‌ها" meta-description="فروشگاه‌های تجهیزات کوهنوردی مرتبط با جامعه کوهنوردان کرمان.">
    <x-site.listing
        title="فروشگاه‌های تجهیزات"
        :action="route('shops.index')"
        :paginator="$shops"
        empty-message="فروشگاهی با این مشخصات پیدا نشد."
        :filters="[
            ['type' => 'search', 'name' => 'q', 'label' => 'نام فروشگاه یا شهر', 'placeholder' => 'مثلاً کوهستان...'],
            ['type' => 'select', 'name' => 'city', 'label' => 'شهر', 'all' => 'همه شهرها', 'options' => $cities->all()],
            ['type' => 'toggle', 'name' => 'verified', 'label' => 'فقط تأییدشده'],
        ]"
    >
        @foreach ($shops as $shop)
            <x-cards.shop :shop="$shop" />
        @endforeach
    </x-site.listing>
</x-layouts.site>
