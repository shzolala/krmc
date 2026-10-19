@props([
    'sponsors',
    'rows' => 2,
])

@php
    $base = $sponsors->values();

    while ($base->isNotEmpty() && $base->count() < 8) {
        $base = $base->concat($sponsors)->values();
    }

    $lines = $rows > 1 && $sponsors->count() >= 5
        ? [[$base, false, 52], [$base->reverse()->values(), true, 44]]
        : [[$base, false, 48]];
@endphp

@if ($sponsors->isNotEmpty())
    <div {{ $attributes->class('space-y-4') }} role="list" aria-label="حامیان کوهستان">
        @foreach ($lines as [$items, $reverse, $duration])
            <div dir="ltr" @class(['logo-marquee', 'logo-marquee--reverse' => $reverse]) style="--marquee-duration: {{ $duration }}s">
                <div class="logo-marquee__track">
                    @foreach ([false, true] as $isClone)
                        @foreach ($items as $sponsor)
                            <a href="{{ route('sponsors.show', $sponsor) }}"
                                @if ($isClone) aria-hidden="true" tabindex="-1" @else role="listitem" @endif
                                title="{{ $sponsor->name }}"
                                class="group mx-2 flex h-24 w-48 shrink-0 items-center justify-center rounded-2xl bg-white px-6 ring-1 ring-navy-900/5 transition duration-500 hover:-translate-y-1 hover:shadow-xl hover:shadow-navy-900/10 focus:outline-none focus-visible:ring-2 focus-visible:ring-accent">
                                @if ($sponsor->logo_url)
                                    <img src="{{ $sponsor->logo_url }}" alt="{{ $isClone ? '' : $sponsor->name }}" loading="lazy" draggable="false"
                                        class="max-h-14 w-auto max-w-full object-contain opacity-60 grayscale transition duration-500 group-hover:scale-105 group-hover:opacity-100 group-hover:grayscale-0">
                                @else
                                    <span dir="rtl" class="line-clamp-2 text-center text-sm font-bold text-navy-900/60 transition group-hover:text-navy-900">{{ $sponsor->name }}</span>
                                @endif
                            </a>
                        @endforeach
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
@endif
