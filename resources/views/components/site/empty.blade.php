@props([
    'message' => 'موردی برای نمایش وجود ندارد.',
    'href' => null,
    'linkLabel' => 'پاک کردن فیلترها',
])

<div {{ $attributes->class(['rounded-3xl bg-white p-10 text-center ring-1 ring-navy-900/5']) }}>
    <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-sand text-muted">
        <x-site.icon name="search" />
    </span>
    <p class="mt-4 text-sm text-muted">{{ $message }}</p>
    @if ($href)
        <a href="{{ $href }}" class="mt-4 inline-block rounded-full bg-navy-900 px-5 py-2 text-xs font-bold text-white transition hover:bg-navy-700">{{ $linkLabel }}</a>
    @endif
</div>
