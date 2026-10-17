@props([
    'title',
    'subtitle' => null,
    'icon' => null,
    'back' => null,
    'backLabel' => 'بازگشت',
])

<div class="flex flex-wrap items-center justify-between gap-4">
    <div class="flex min-w-0 items-center gap-3">
        @if ($icon)
            <span class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-navy-900 text-brand shadow-sm dark:bg-zinc-800">
                <flux:icon :name="$icon" class="size-6" />
            </span>
        @endif
        <div class="min-w-0">
            @if ($back)
                <a href="{{ $back }}" wire:navigate class="mb-0.5 inline-flex items-center gap-1 text-xs text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-200">
                    <flux:icon.arrow-right class="size-3.5" />
                    {{ $backLabel }}
                </a>
            @endif
            <h1 class="truncate text-xl font-bold text-zinc-900 sm:text-2xl dark:text-zinc-50">{{ $title }}</h1>
            @if ($subtitle)
                <p class="mt-0.5 text-sm text-zinc-500">{{ $subtitle }}</p>
            @endif
        </div>
    </div>

    <div class="flex flex-wrap items-center gap-2">
        {{ $slot }}
        <x-admin.role-switcher />
    </div>
</div>
