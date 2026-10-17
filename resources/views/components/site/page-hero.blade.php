@props([
    'title',
    'eyebrow' => null,
    'description' => null,
    'cover' => null,
    'section' => null,
    'crumbs' => [],
    'size' => 'md',
    'overlap' => false,
    'badges' => null,
    'actions' => null,
])

@php
    $cover ??= asset('images/covers/'.($section ? \App\Support\SiteNavigation::section($section)['image'] : 'hero.jpg'));
    $top = $size === 'lg' ? 'pt-10 sm:pt-14 lg:pt-16' : 'pt-5 sm:pt-6';
    $bottom = $overlap ? 'pb-16 sm:pb-[4.5rem]' : ($size === 'lg' ? 'pb-14 sm:pb-16' : 'pb-8 sm:pb-10');
@endphp

<section {{ $attributes->class(['relative isolate overflow-hidden bg-navy-950 text-white']) }}>
    <img src="{{ $cover }}" alt="" class="absolute inset-0 -z-10 h-full w-full object-cover">
    <div class="hero-overlay absolute inset-0 -z-10"></div>

    <div @class(['mx-auto max-w-7xl px-4 sm:px-6 lg:px-8', $top, $bottom])>
        <x-site.breadcrumbs :items="$crumbs" />

        @if ($badges)
            <div class="mt-5 flex flex-wrap items-center gap-2">{{ $badges }}</div>
        @endif

        @if ($eyebrow)
            <p class="mt-3 inline-flex items-center gap-2 rounded-full bg-white/10 px-3.5 py-1.5 text-xs font-semibold text-accent-soft ring-1 ring-white/15 backdrop-blur">
                <span class="h-1.5 w-1.5 rounded-full bg-accent"></span>
                {{ $eyebrow }}
            </p>
        @endif

        <h1 @class(['max-w-3xl font-extrabold leading-[1.35] text-balance', 'mt-4 text-3xl sm:text-4xl lg:text-5xl' => $size === 'lg', 'mt-2 text-2xl sm:text-3xl' => $size !== 'lg'])>{{ $title }}</h1>

        @if ($description)
            <p @class(['max-w-2xl text-sm text-white/80', 'mt-4 leading-8 sm:text-base' => $size === 'lg', 'mt-1.5 leading-7' => $size !== 'lg'])>{{ $description }}</p>
        @endif

        @if ($actions)
            <div class="mt-7 flex flex-wrap items-center gap-3">{{ $actions }}</div>
        @endif

        @if (! $slot->isEmpty())
            <div class="mt-8">{{ $slot }}</div>
        @endif
    </div>
</section>
