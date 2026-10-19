@props([
    'title',
    'action',
    'filters' => [],
    'paginator' => null,
    'grid' => 'sm:grid-cols-2 xl:grid-cols-3',
    'emptyMessage' => 'موردی با این مشخصات پیدا نشد.',
])

{{--
    Directory page: plain header, results on the right and a floating filter panel on the left
    (a bottom sheet on mobile). Filters are described declaratively:
      ['type' => 'search',  'name' => 'q', 'label' => '...', 'placeholder' => '...']
      ['type' => 'options', 'name' => 'type', 'label' => '...', 'options' => [value => label], 'all' => 'همه']  (3 per row)
      ['type' => 'select',  'name' => 'region', 'label' => '...', 'options' => [value => label], 'all' => 'همه مناطق']
      ['type' => 'toggle',  'name' => 'verified', 'label' => '...']
--}}
@php
    $names = collect($filters)->pluck('name')->all();
    $query = array_filter(request()->only($names), fn ($value): bool => filled($value));
    $url = fn (array $except = [], array $extra = []): string => $action.(($merged = array_merge(\Illuminate\Support\Arr::except($query, $except), $extra)) ? '?'.http_build_query($merged) : '');

    $chips = collect($filters)->map(function (array $filter) use ($query, $url): ?array {
        $value = $query[$filter['name']] ?? null;

        if ($value === null) {
            return null;
        }

        $label = match ($filter['type']) {
            'search' => '«'.$value.'»',
            'toggle' => $filter['label'],
            default => $filter['options'][$value] ?? null,
        };

        return $label ? ['label' => $label, 'href' => $url([$filter['name']])] : null;
    })->filter()->values();

    $pill = 'block cursor-pointer rounded-lg px-3 py-1.5 text-center text-xs font-semibold text-navy-800 ring-1 ring-navy-900/10 transition hover:bg-sand peer-checked:bg-navy-900 peer-checked:text-white peer-checked:ring-navy-900 peer-focus-visible:ring-2 peer-focus-visible:ring-accent';
    $field = 'w-full rounded-lg border-0 bg-sand px-3 py-2 text-[13px] text-ink outline-none ring-1 ring-navy-900/10 transition placeholder:text-muted/70 focus:bg-white focus:ring-2 focus:ring-accent';
@endphp

<header class="mx-auto max-w-[120rem] px-4 pt-6 sm:px-5 lg:pt-8">
    <h1 class="text-2xl font-extrabold text-navy-900 sm:text-3xl">{{ $title }}</h1>
</header>

<section class="mx-auto max-w-[120rem] px-4 pb-8 pt-5 sm:px-5 sm:pb-10">
    <div class="lg:grid lg:grid-cols-[minmax(0,1fr)_18rem] lg:items-start lg:gap-6 xl:grid-cols-[minmax(0,1fr)_20rem]">
        {{-- Results (right in RTL) --}}
        <div class="min-w-0">
            <div @class(['flex flex-wrap items-center justify-between gap-3', 'mb-5', 'lg:hidden' => $chips->isEmpty()])>
                <div class="flex flex-wrap items-center gap-2" aria-label="فیلترهای فعال">
                    @foreach ($chips as $chip)
                        <a href="{{ $chip['href'] }}" class="inline-flex items-center gap-1.5 rounded-full bg-white py-1 pl-2 pr-3 text-xs font-semibold text-navy-900 ring-1 ring-navy-900/10 transition hover:bg-navy-900 hover:text-white" aria-label="حذف فیلتر {{ $chip['label'] }}">
                            {{ $chip['label'] }}
                            <x-site.icon name="close" class="h-3.5 w-3.5" />
                        </a>
                    @endforeach
                    @if ($chips->isNotEmpty())
                        <a href="{{ $action }}" class="px-2 text-xs font-bold text-accent hover:underline">پاک کردن همه</a>
                    @endif
                </div>

                <div class="flex items-center gap-2">
                    <button type="button" data-filter-open aria-controls="listing-filters" aria-expanded="false" class="inline-flex items-center gap-2 rounded-full bg-navy-900 px-4 py-2 text-xs font-bold text-white transition hover:bg-navy-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-accent focus-visible:ring-offset-2 lg:hidden">
                        <x-site.icon name="search" class="h-4 w-4" />
                        فیلترها
                        @if ($chips->isNotEmpty())
                            <span class="flex h-5 min-w-5 items-center justify-center rounded-full bg-accent px-1 text-[11px] text-navy-950">{{ fa_num($chips->count()) }}</span>
                        @endif
                    </button>
                </div>
            </div>

            @if ($paginator && $paginator->isEmpty())
                <x-site.empty :message="$emptyMessage" :href="$action" />
            @else
                <div class="grid gap-5 {{ $grid }}">{{ $slot }}</div>
            @endif

            @if ($paginator)
                <div class="mt-10">{{ $paginator->links() }}</div>
            @endif
        </div>

        {{-- Floating filter (left in RTL); a bottom sheet on mobile --}}
        <div data-filter-backdrop class="pointer-events-none fixed inset-0 z-40 bg-navy-950/55 opacity-0 transition-opacity duration-300 data-[open=true]:pointer-events-auto data-[open=true]:opacity-100 lg:hidden"></div>

        <aside
            id="listing-filters"
            data-filter-sheet
            aria-label="فیلترها"
            class="max-lg:invisible max-lg:fixed max-lg:inset-x-0 max-lg:bottom-0 max-lg:z-50 max-lg:max-h-[88svh] max-lg:translate-y-full max-lg:overflow-y-auto max-lg:rounded-t-3xl max-lg:bg-white max-lg:shadow-2xl max-lg:transition-transform max-lg:duration-300 max-lg:data-[open=true]:visible max-lg:data-[open=true]:translate-y-0 lg:sticky lg:top-24 lg:max-h-[calc(100svh-7rem)] lg:overflow-y-auto lg:rounded-3xl lg:bg-white lg:shadow-xl lg:shadow-navy-900/10 lg:ring-1 lg:ring-navy-900/5"
        >
            <form action="{{ $action }}" method="get" role="search" data-autosubmit class="flex flex-col">
                <div class="flex items-center justify-between border-b border-navy-900/5 px-4 py-3">
                    <h2 class="text-sm font-extrabold text-navy-900">فیلترها</h2>
                    <div class="flex items-center gap-3">
                        @if ($chips->isNotEmpty())
                            <a href="{{ $action }}" class="text-xs font-bold text-accent hover:underline">پاک کردن</a>
                        @endif
                        <button type="button" data-filter-close aria-label="بستن فیلترها" class="flex h-8 w-8 items-center justify-center rounded-full bg-sand text-navy-900 transition hover:bg-navy-900/10 focus:outline-none focus-visible:ring-2 focus-visible:ring-accent lg:hidden">
                            <x-site.icon name="close" class="h-4 w-4" />
                        </button>
                    </div>
                </div>

                <div class="flex flex-col gap-4 px-4 py-4">
                    @foreach ($filters as $filter)
                        @php($active = (string) request()->query($filter['name'], ''))

                        @switch($filter['type'])
                            @case('search')
                                <label class="block">
                                    <span class="mb-1.5 block text-[11px] font-bold text-muted">{{ $filter['label'] }}</span>
                                    <span class="relative block">
                                        <x-site.icon name="search" class="pointer-events-none absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted" />
                                        <input type="search" name="{{ $filter['name'] }}" value="{{ $active }}" placeholder="{{ $filter['placeholder'] ?? '' }}" class="{{ $field }} pr-9">
                                    </span>
                                </label>
                                @break

                            @case('options')
                                <fieldset>
                                    <legend class="mb-1.5 text-[11px] font-bold text-muted">{{ $filter['label'] }}</legend>
                                    <div class="flex flex-wrap gap-1.5">
                                        @foreach (['' => $filter['all'] ?? 'همه'] + $filter['options'] as $value => $label)
                                            <label>
                                                <input type="radio" name="{{ $filter['name'] }}" value="{{ $value }}" class="peer sr-only" @checked($active === (string) $value)>
                                                <span class="{{ $pill }}">{{ $label }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </fieldset>
                                @break

                            @case('select')
                                <label class="block">
                                    <span class="mb-1.5 block text-[11px] font-bold text-muted">{{ $filter['label'] }}</span>
                                    <select name="{{ $filter['name'] }}" class="{{ $field }}">
                                        <option value="">{{ $filter['all'] ?? 'همه' }}</option>
                                        @foreach ($filter['options'] as $value => $label)
                                            <option value="{{ $value }}" @selected($active === (string) $value)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </label>
                                @break

                            @case('toggle')
                                <label class="flex cursor-pointer items-center gap-2.5 text-[13px] font-semibold text-navy-900">
                                    <input type="checkbox" name="{{ $filter['name'] }}" value="1" class="h-4 w-4 rounded border-navy-900/20 accent-navy-900" @checked($active === '1')>
                                    {{ $filter['label'] }}
                                </label>
                                @break
                        @endswitch
                    @endforeach

                </div>

                <div class="sticky bottom-0 border-t border-navy-900/5 bg-white px-4 py-3 lg:static">
                    <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-lg bg-navy-900 px-6 py-2.5 text-sm font-bold text-white transition hover:bg-navy-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-accent focus-visible:ring-offset-2">
                        <x-site.icon name="search" class="h-4 w-4" />
                        اعمال فیلترها
                    </button>
                </div>
            </form>
        </aside>
    </div>
</section>
