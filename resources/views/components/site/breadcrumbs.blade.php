@props([
    'items' => [],
    'tone' => 'light',
])

<nav aria-label="مسیر صفحه" @class(['text-xs', 'text-white/65' => $tone === 'light', 'text-muted' => $tone === 'dark'])>
    <ol class="flex flex-wrap items-center gap-x-2 gap-y-1">
        <li><a href="{{ route('home') }}" @class(['transition', 'hover:text-white' => $tone === 'light', 'hover:text-accent' => $tone === 'dark'])>خانه</a></li>
        @foreach ($items as $label => $url)
            <li aria-hidden="true">/</li>
            <li>
                @if ($url)
                    <a href="{{ $url }}" @class(['transition', 'hover:text-white' => $tone === 'light', 'hover:text-accent' => $tone === 'dark'])>{{ $label }}</a>
                @else
                    <span @class(['text-white/90' => $tone === 'light', 'text-navy-900' => $tone === 'dark']) aria-current="page">{{ $label }}</span>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
