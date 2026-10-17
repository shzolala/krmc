@props([
    'title' => null,
    'description' => null,
    'padded' => true,
])

<section {{ $attributes->class('rounded-xl border border-zinc-200 bg-white shadow-xs dark:border-zinc-800 dark:bg-zinc-900') }}>
    @if ($title)
        <header class="flex flex-wrap items-center justify-between gap-2 border-b border-zinc-100 px-5 py-3.5 dark:border-zinc-800">
            <div>
                <h2 class="text-sm font-bold text-zinc-900 dark:text-zinc-50">{{ $title }}</h2>
                @if ($description)
                    <p class="mt-0.5 text-xs text-zinc-500">{{ $description }}</p>
                @endif
            </div>
            @isset($actions)
                <div class="flex items-center gap-2">{{ $actions }}</div>
            @endisset
        </header>
    @endif
    <div @class(['p-5' => $padded])>
        {{ $slot }}
    </div>
</section>
