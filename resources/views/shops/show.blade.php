<x-layouts.site :title="$shop->name" :meta-description="$shop->summary">
    <x-site.detail-header
        :title="$shop->name"
        :crumbs="['فروشگاه‌ها' => route('shops.index'), $shop->name => null]"
        kicker="فروشگاه تجهیزات"
        :cover="\App\Support\CatalogCover::for('shop-'.$shop->slug)"
        :seed="'shop-'.$shop->slug"
        :reviewable="$shop"
    >
        <x-slot:badges>
            @if ($shop->is_premium)<x-badge tone="premium">ویژه</x-badge>@endif
            @if ($shop->is_verified)<x-badge tone="verified">تأییدشده</x-badge>@endif
        </x-slot:badges>
        <x-slot:meta>
            @if ($shop->city)
                <span class="inline-flex items-center gap-1.5"><x-site.icon name="pin" class="h-4 w-4 text-accent" />{{ $shop->city }}</span>
            @endif
        </x-slot:meta>
    </x-site.detail-header>

    <x-site.detail-layout>
        <x-site.panel title="درباره فروشگاه" icon="bag">
            <p class="text-sm leading-8 text-muted">{{ $shop->summary ?: 'پروفایل فروشگاه — جزئیات محصولات، تبلیغات و پلن نمایش در گام‌های بعدی تکمیل می‌شود.' }}</p>
        </x-site.panel>

        <x-slot:aside>
            <x-site.panel title="اطلاعات" icon="document">
                <x-site.facts :columns="1" :items="[
                    ['label' => 'شهر', 'value' => $shop->city, 'icon' => 'pin'],
                    ['label' => 'آدرس', 'value' => $shop->address, 'icon' => 'pin'],
                ]" />
            </x-site.panel>

            @if ($shop->latitude && $shop->longitude)
                <x-site.map-panel :markers="[$shop->toMapMarker('shop', route('shops.show', $shop))]" :zoom="14" height="h-64" />
            @endif
        </x-slot:aside>
    </x-site.detail-layout>

    <x-site.section spacing="pt-14">
        <livewire:review.section :reviewable="$shop" :key="'shop-reviews-'.$shop->id" />
    </x-site.section>
</x-layouts.site>
