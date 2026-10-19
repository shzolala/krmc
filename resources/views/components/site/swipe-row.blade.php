@props([
    'from' => 'sm',
    'tag' => 'div',
    'wide' => false,
    'label' => null,
    'moreHref' => null,
    'moreLabel' => 'مشاهده همه',
    'moreAlways' => false,
])

{{--
    Mobile: one horizontally swipeable row (scroll-snap) instead of a long vertical stack.
    From the `from` breakpoint on it becomes a normal grid; pass the grid columns via class.
--}}
@php
    $base = '-mx-4 flex snap-x snap-mandatory scroll-px-4 gap-4 overflow-x-auto px-4 pb-3 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden *:shrink-0 *:snap-start';

    $classes = match ($from) {
        'lg' => $base.($wide ? ' *:w-[88%] sm:-mx-6 sm:scroll-px-6 sm:px-6 sm:*:w-96' : ' *:w-[80%] sm:-mx-6 sm:scroll-px-6 sm:px-6 sm:*:w-72').' lg:mx-0 lg:grid lg:overflow-visible lg:px-0 lg:pb-0 lg:*:w-auto',
        'md' => $base.($wide ? ' *:w-[88%] sm:-mx-6 sm:scroll-px-6 sm:px-6 sm:*:w-96' : ' *:w-[80%] sm:-mx-6 sm:scroll-px-6 sm:px-6 sm:*:w-72').' md:mx-0 md:grid md:overflow-visible md:px-0 md:pb-0 md:*:w-auto',
        default => $base.($wide ? ' *:w-[88%]' : ' *:w-[80%]').' sm:mx-0 sm:grid sm:overflow-visible sm:px-0 sm:pb-0 sm:*:w-auto',
    };
@endphp

@php
    $hideMore = $moreAlways ? '' : match ($from) {
        'lg' => 'lg:hidden',
        'md' => 'md:hidden',
        default => 'sm:hidden',
    };
@endphp

<{{ $tag }} {{ $attributes->class($classes) }} @if ($label) aria-label="{{ $label }}" @endif>
    {{ $slot }}
    @if ($moreHref)
        @if ($tag === 'ul') <li class="contents"> @endif
        <a href="{{ $moreHref }}" @class(['!w-36 group flex min-h-40 flex-col items-center justify-center gap-2 rounded-3xl border border-dashed border-navy-900/20 bg-white/60 text-sm font-bold text-navy-900 transition hover:border-accent hover:bg-white focus:outline-none focus-visible:ring-2 focus-visible:ring-accent', $hideMore])>
            <span class="flex h-10 w-10 items-center justify-center rounded-full bg-navy-900 text-white transition group-hover:bg-accent group-hover:text-navy-950"><x-site.icon name="arrow-left" class="h-4 w-4" /></span>
            {{ $moreLabel }}
        </a>
        @if ($tag === 'ul') </li> @endif
    @endif
</{{ $tag }}>
