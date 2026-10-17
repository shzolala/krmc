@props([
    'eyebrow' => null,
    'title' => null,
    'description' => null,
    'href' => null,
    'linkLabel' => 'مشاهده همه',
    'id' => null,
    'spacing' => 'pt-16 sm:pt-20',
])

<section @if ($id) id="{{ $id }}" @endif {{ $attributes->class(['mx-auto max-w-7xl scroll-mt-24 px-4 sm:px-6 lg:px-8', $spacing]) }}>
    @if ($title)
        <x-site.section-heading :eyebrow="$eyebrow" :title="$title" :description="$description" :href="$href" :link-label="$linkLabel" />
    @endif

    {{ $slot }}
</section>
