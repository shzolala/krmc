@props([
    'label',
    'value',
    'icon' => 'chart-bar',
    'tone' => 'zinc',
    'hint' => null,
    'href' => null,
])

@php
    $tones = [
        'zinc' => 'bg-zinc-100 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300',
        'blue' => 'bg-blue-100 text-blue-700 dark:bg-blue-950 dark:text-blue-300',
        'green' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300',
        'amber' => 'bg-amber-100 text-amber-700 dark:bg-amber-950 dark:text-amber-300',
        'purple' => 'bg-violet-100 text-violet-700 dark:bg-violet-950 dark:text-violet-300',
        'red' => 'bg-red-100 text-red-700 dark:bg-red-950 dark:text-red-300',
        'teal' => 'bg-teal-100 text-teal-700 dark:bg-teal-950 dark:text-teal-300',
    ];
@endphp

<{{ $href ? 'a' : 'div' }} @if ($href) href="{{ $href }}" wire:navigate @endif
    class="group flex items-center gap-4 rounded-xl border border-zinc-200 bg-white p-4 shadow-xs transition dark:border-zinc-800 dark:bg-zinc-900 {{ $href ? 'hover:border-zinc-300 hover:shadow-sm dark:hover:border-zinc-700' : '' }}">
    <span class="flex size-12 shrink-0 items-center justify-center rounded-xl {{ $tones[$tone] ?? $tones['zinc'] }}">
        <flux:icon :name="$icon" class="size-6" />
    </span>
    <span class="min-w-0">
        <span class="block text-xs text-zinc-500">{{ $label }}</span>
        <span class="block text-2xl font-extrabold leading-tight text-zinc-900 dark:text-zinc-50">{{ $value }}</span>
        @if ($hint)
            <span class="block truncate text-xs text-zinc-500">{{ $hint }}</span>
        @endif
    </span>
</{{ $href ? 'a' : 'div' }}>
