@props(['row'])

@php
    $badge = $row['badge'];
    $earned = $row['earned'];
@endphp

<li {{ $attributes->class('flex items-center gap-3') }}>
    <span @class([
        'flex h-11 w-11 shrink-0 items-center justify-center rounded-full ring-1 ring-inset transition',
        $badge->color->classes() => $earned,
        'bg-navy-900/5 text-navy-900/30 ring-navy-900/10' => ! $earned,
    ])>
        @if ($earned)
            <flux:icon :name="$badge->icon" variant="mini" class="size-5" />
        @else
            <flux:icon.lock-closed variant="mini" class="size-4" />
        @endif
    </span>
    <div class="min-w-0 flex-1">
        <p @class(['text-sm font-bold', 'text-navy-900' => $earned, 'text-navy-900/55' => ! $earned])>{{ $badge->name }}</p>
        <p class="text-xs text-muted">{{ $badge->requirementText() }}</p>
        @unless ($earned)
            <div class="mt-1.5 h-1 overflow-hidden rounded-full bg-navy-900/10" role="progressbar" aria-valuenow="{{ $row['percent'] }}" aria-valuemin="0" aria-valuemax="100" aria-label="پیشرفت {{ $badge->name }}">
                <div class="h-full rounded-full bg-navy-800/60" style="width: {{ $row['percent'] }}%"></div>
            </div>
        @endunless
    </div>
    @unless ($earned)
        <span class="shrink-0 text-xs tabular-nums text-muted">{{ fa_num($row['current']) }}/{{ fa_num($row['required']) }}</span>
    @endunless
</li>
