@props([
    'program',
])

@php
    $isOngoing = $program->status === \App\Enums\ProgramStatus::Ongoing;
    $remaining = $program->capacity ? max($program->capacity - $program->registered_count, 0) : null;
    $fill = $program->capacity ? min(100, round($program->registered_count / $program->capacity * 100)) : null;
    $parts = $program->starts_at ? explode(' ', fa_date($program->starts_at, monthName: true)) : [];
@endphp

<li>
    <a href="{{ route('programs.show', $program) }}" {{ $attributes->class(['group grid grid-cols-[4.5rem_1fr] items-center gap-4 rounded-2xl bg-white p-3 ring-1 ring-navy-900/5 transition hover:shadow-lg hover:shadow-navy-900/10 hover:ring-accent/50 sm:grid-cols-[4.5rem_1fr_11rem] sm:gap-5']) }}>
        <span @class([
            'flex h-[4.5rem] flex-col items-center justify-center rounded-xl text-center',
            'bg-emerald-600 text-white' => $isOngoing,
            'bg-navy-900 text-white' => ! $isOngoing,
        ])>
            @if ($isOngoing)
                <span class="h-2 w-2 animate-pulse rounded-full bg-white"></span>
                <span class="mt-1.5 text-[11px] font-bold leading-4">در جریان</span>
            @elseif ($parts)
                <span class="text-2xl font-extrabold leading-none">{{ $parts[0] }}</span>
                <span class="mt-1 text-[11px] font-medium text-white/75">{{ $parts[1] }}</span>
            @endif
        </span>

        <span class="min-w-0">
            <span class="block truncate text-base font-bold text-navy-900 transition group-hover:text-accent">{{ $program->title }}</span>
            <span class="mt-1 block truncate text-xs text-muted">
                {{ collect([$program->destination?->name, $program->club?->name])->filter()->implode(' · ') }}
            </span>
            @if ($program->starts_at)
                <span class="mt-1 block text-xs text-muted">
                    {{ fa_date_range($program->starts_at, $program->ends_at) }}
                    · {{ $program->starts_at->isFuture() ? fa_num((int) ceil(now()->diffInDays($program->starts_at, true))).' روز دیگر' : 'شروع‌شده' }}
                </span>
            @endif
        </span>

        @if ($fill !== null)
            <span class="col-span-2 sm:col-span-1">
                <span class="flex items-baseline justify-between text-xs">
                    <span class="font-bold text-navy-900">{{ fa_num($remaining) }} جای خالی</span>
                    <span class="text-muted">از {{ fa_num($program->capacity) }}</span>
                </span>
                <span class="mt-1.5 block h-1.5 overflow-hidden rounded-full bg-sand" role="progressbar" aria-valuenow="{{ $fill }}" aria-valuemin="0" aria-valuemax="100">
                    <span @class(['block h-full rounded-full', 'bg-red-500' => $fill >= 85, 'bg-accent' => $fill < 85]) style="width: {{ $fill }}%"></span>
                </span>
            </span>
        @endif
    </a>
</li>
