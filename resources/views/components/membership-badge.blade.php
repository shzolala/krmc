@props([
    'user' => null,
    'plan' => null,
    'onDark' => false,
    'showText' => true,
    'public' => false,
])

@php
    $plan ??= auth()->check() ? $user?->currentMembershipPlan() : null;
    $visible = $plan && ($public || auth()->check());
@endphp

@if ($visible)
    <span
        title="سطح عضویت: {{ $plan->name }}"
        {{ $attributes->class([
            'inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[11px] font-medium leading-5 ring-1 ring-inset whitespace-nowrap',
            $plan->badge_color->classes($onDark),
        ]) }}
    >
        <flux:icon :name="$plan->badge_icon" variant="mini" class="size-3 shrink-0" />
        @if ($showText)
            {{ $plan->badgeText() }}
        @else
            <span class="sr-only">{{ $plan->badgeText() }}</span>
        @endif
    </span>
@endif
