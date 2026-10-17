@props([
    'action',
    'placeholder' => 'جستجو...',
    'filters' => [],
    'query' => null,
    'hidden' => [],
    'total' => null,
])

{{--
    Search + filter chips shown right under a page hero.
    $filters: list of ['label' => string, 'href' => string, 'active' => bool]
    $hidden: extra query params (name => value) preserved when searching.
--}}
<section class="relative z-10 mx-auto -mt-10 max-w-7xl px-4 sm:px-6 lg:px-8">
    <div class="rounded-3xl bg-white p-3 shadow-2xl shadow-navy-900/15 ring-1 ring-navy-900/5 sm:p-4">
        <form action="{{ $action }}" method="get" role="search" class="flex flex-col gap-3 sm:flex-row">
            @foreach ($hidden as $name => $value)
                @if (filled($value))
                    <input type="hidden" name="{{ $name }}" value="{{ $value }}">
                @endif
            @endforeach

            <label class="relative block flex-1">
                <span class="sr-only">{{ $placeholder }}</span>
                <x-site.icon name="search" class="pointer-events-none absolute right-3.5 top-1/2 h-[18px] w-[18px] -translate-y-1/2 text-muted" />
                <input
                    type="search"
                    name="q"
                    value="{{ $query }}"
                    placeholder="{{ $placeholder }}"
                    class="w-full rounded-xl border-0 bg-sand py-3 pl-3.5 pr-11 text-sm text-ink outline-none ring-1 ring-navy-900/10 transition placeholder:text-muted/70 focus:bg-white focus:ring-2 focus:ring-accent"
                >
            </label>
            <button type="submit" class="rounded-xl bg-navy-900 px-8 py-3 text-sm font-bold text-white transition hover:bg-navy-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-accent focus-visible:ring-offset-2">
                جستجو
            </button>
        </form>

        @if (count($filters) > 0 || $total !== null)
            <div class="mt-3 flex flex-wrap items-center justify-between gap-3 border-t border-navy-900/5 pt-3">
                <div class="flex flex-wrap items-center gap-2">
                    @foreach ($filters as $filter)
                        <a href="{{ $filter['href'] }}" @class([
                            'rounded-full px-4 py-1.5 text-xs font-semibold transition',
                            'bg-navy-900 text-white' => $filter['active'],
                            'bg-sand text-navy-800 hover:bg-navy-900/10' => ! $filter['active'],
                        ]) @if ($filter['active']) aria-current="true" @endif>{{ $filter['label'] }}</a>
                    @endforeach
                </div>
                @if ($total !== null)
                    <p class="text-xs text-muted">{{ fa_num($total) }} مورد</p>
                @endif
            </div>
        @endif
    </div>
</section>
