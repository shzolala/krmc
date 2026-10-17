@props([
    'shop',
])

<x-site.media-card
    :href="route('shops.show', $shop)"
    :reviewable="$shop"
    :title="$shop->name"
    :image="\App\Support\CatalogCover::for('shop-'.$shop->slug)"
    kicker="فروشگاه تجهیزات"
    :summary="$shop->summary"
    :corner="$shop->city"
    {{ $attributes }}
>
    @if ($shop->is_verified)
        <x-slot:badges>
            <span class="rounded-full bg-emerald-500 px-2.5 py-0.5 text-[11px] font-semibold text-white">تأییدشده</span>
        </x-slot:badges>
    @endif
</x-site.media-card>
