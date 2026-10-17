@props([
    'title',
    'crumbs' => [],
    'kicker' => null,
    'cover' => null,
    'seed' => null,
    'reviewable' => null,
    'badges' => null,
    'meta' => null,
    'actions' => null,
    'facts' => [],
])

<header class="mx-auto max-w-7xl px-4 pt-6 sm:px-6 lg:px-8 lg:pt-8">
    <x-site.breadcrumbs :items="$crumbs" tone="dark" />

    <div class="mt-4 flex flex-wrap items-start justify-between gap-4">
        <div class="min-w-0">
            @if ($badges)
                <div class="mb-2 flex flex-wrap items-center gap-1.5">{{ $badges }}</div>
            @endif
            @if ($kicker)
                <p class="text-xs font-semibold text-accent">{{ $kicker }}</p>
            @endif
            <h1 class="text-2xl font-extrabold leading-9 text-navy-900 sm:text-3xl">{{ $title }}</h1>

            <div class="mt-2 flex flex-wrap items-center gap-x-4 gap-y-1.5 text-sm text-muted">
                @if ($reviewable)
                    <x-site.rating :model="$reviewable" href="#reviews" size="lg" class="text-navy-900" />
                @endif
                {{ $meta }}
            </div>
        </div>

        @if ($actions)
            <div class="flex flex-wrap items-center gap-2">{{ $actions }}</div>
        @endif
    </div>

    @php($facts = collect($facts)->filter(fn ($fact) => filled($fact['value'] ?? null)))
    @if ($facts->isNotEmpty())
        <dl class="mt-4 flex flex-wrap gap-2">
            @foreach ($facts as $fact)
                <div class="rounded-2xl bg-sand px-4 py-2">
                    <dt class="text-[11px] font-semibold text-muted">{{ $fact['label'] }}</dt>
                    <dd class="text-sm font-extrabold text-navy-900">{{ $fact['value'] }}</dd>
                </div>
            @endforeach
        </dl>
    @endif

    <x-site.photo-gallery :cover="$cover" :seed="$seed ?? $title" :alt="$title" class="mt-5" />
</header>
