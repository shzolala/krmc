@props([
    'cover' => null,
    'seed',
    'alt' => '',
])

@php
    $photos = \App\Support\CatalogCover::gallery($cover, $seed, 5);
@endphp

<div
    x-data="{ open: false, index: 0, photos: @js($photos) }"
    x-on:keydown.escape.window="open = false"
    x-on:keydown.arrow-left.window="open && (index = (index + 1) % photos.length)"
    x-on:keydown.arrow-right.window="open && (index = (index - 1 + photos.length) % photos.length)"
    {{ $attributes }}
>
    <div class="grid h-56 grid-cols-4 grid-rows-2 gap-2 overflow-hidden rounded-3xl sm:h-72 lg:h-80">
        @foreach ($photos as $i => $photo)
            <button
                type="button"
                x-on:click="index = {{ $i }}; open = true"
                @class([
                    'group relative overflow-hidden bg-navy-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-accent',
                    'col-span-4 row-span-2 sm:col-span-2' => $i === 0,
                    'hidden sm:block' => $i > 0,
                ])
                aria-label="نمایش عکس {{ fa_num($i + 1) }}"
            >
                <img src="{{ $photo }}" alt="{{ $i === 0 ? $alt : '' }}" loading="{{ $i === 0 ? 'eager' : 'lazy' }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                @if ($i === count($photos) - 1)
                    <span class="absolute bottom-2 left-2 inline-flex items-center gap-1.5 rounded-full bg-white/90 px-3 py-1 text-[11px] font-bold text-navy-900 backdrop-blur">
                        <x-site.icon name="image" class="h-3.5 w-3.5" />
                        همه عکس‌ها ({{ fa_num(count($photos)) }})
                    </span>
                @endif
            </button>
        @endforeach
    </div>

    <div x-show="open" x-cloak x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center bg-navy-950/90 p-4" x-on:click.self="open = false" role="dialog" aria-modal="true">
        <button type="button" class="absolute right-4 top-4 rounded-full bg-white/10 p-2 text-white hover:bg-white/20" x-on:click="open = false" aria-label="بستن">
            <x-site.icon name="close" class="h-5 w-5" />
        </button>
        <img x-bind:src="photos[index]" alt="" class="max-h-[85vh] max-w-full rounded-2xl object-contain">
        <p class="absolute bottom-4 left-1/2 -translate-x-1/2 rounded-full bg-white/10 px-3 py-1 text-xs text-white" x-text="(index + 1) + ' / ' + photos.length"></p>
    </div>
</div>
