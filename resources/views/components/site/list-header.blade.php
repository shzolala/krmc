@props([
    'title',
    'description' => null,
    'crumbs' => [],
])

<header class="border-b border-navy-900/5 bg-white">
    <div class="mx-auto flex max-w-7xl flex-wrap items-end justify-between gap-4 px-4 py-6 sm:px-6 lg:px-8">
        <div class="min-w-0">
            <x-site.breadcrumbs :items="$crumbs" tone="dark" />
            <h1 class="mt-2 text-2xl font-extrabold text-navy-900 sm:text-3xl">{{ $title }}</h1>
            @if ($description)
                <p class="mt-1.5 max-w-2xl text-sm leading-7 text-muted">{{ $description }}</p>
            @endif
        </div>
        @if (! $slot->isEmpty())
            <div class="flex flex-wrap gap-2">{{ $slot }}</div>
        @endif
    </div>
</header>
