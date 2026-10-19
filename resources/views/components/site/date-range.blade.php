@props([
    'start',
    'end' => null,
])

@php
    $end = $end ?? $start;
    $days = \App\Support\JalaliDate::days($start, $end);
    $dayParts = fn ($date) => explode(' ', fa_date($date, monthName: true));
    [$startDay, $startMonth] = $dayParts($start);
    [$endDay, $endMonth] = $dayParts($end);
    $weekdays = ['یکشنبه', 'دوشنبه', 'سه‌شنبه', 'چهارشنبه', 'پنجشنبه', 'جمعه', 'شنبه'];
    $isPast = $end->isPast();
    $isOngoing = $start->isPast() && ! $isPast;
    $countdown = match (true) {
        $isOngoing => 'در جریان است',
        $isPast => 'پایان‌یافته',
        default => fa_num((int) ceil(now()->diffInDays($start, true))).' روز تا شروع',
    };
    $strip = $days <= 10 ? collect(range(0, $days - 1))->map(fn (int $offset) => $start->copy()->addDays($offset)) : collect();
@endphp

<div {{ $attributes->class('space-y-4') }}>
    <div class="flex items-stretch gap-3">
        <div class="flex-1 rounded-2xl bg-navy-900 p-3 text-center text-white">
            <p class="text-[11px] text-white/60">شروع</p>
            <p class="mt-1 text-2xl font-extrabold leading-none">{{ $startDay }}</p>
            <p class="mt-1 text-xs text-white/75">{{ $startMonth }}</p>
        </div>
        <div class="flex flex-col items-center justify-center gap-1 text-muted">
            <x-site.icon name="arrow-left" class="h-4 w-4" />
            <span class="text-[11px] font-semibold">{{ $days === 1 ? 'یک‌روزه' : fa_num($days).' روز' }}</span>
        </div>
        <div class="flex-1 rounded-2xl bg-sand p-3 text-center text-navy-900">
            <p class="text-[11px] text-muted">پایان</p>
            <p class="mt-1 text-2xl font-extrabold leading-none">{{ $endDay }}</p>
            <p class="mt-1 text-xs text-muted">{{ $endMonth }}</p>
        </div>
    </div>

    @if ($strip->isNotEmpty())
        <ol class="flex gap-1.5" aria-label="روزهای برنامه">
            @foreach ($strip as $date)
                <li class="min-w-0 flex-1 rounded-lg bg-accent/15 py-1.5 text-center">
                    <span class="block truncate text-[10px] text-muted">{{ $weekdays[$date->dayOfWeek] }}</span>
                    <span class="block text-sm font-bold text-navy-900">{{ explode(' ', fa_date($date, monthName: true))[0] }}</span>
                </li>
            @endforeach
        </ol>
    @endif

    <p @class([
        'inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-bold',
        'bg-emerald-100 text-emerald-700' => $isOngoing,
        'bg-accent/15 text-navy-900' => ! $isOngoing && ! $isPast,
        'bg-zinc-100 text-zinc-500' => $isPast,
    ])>{{ $countdown }}</p>

    @if ($start->format('H:i') !== '00:00')
        <p class="text-xs text-muted">ساعت شروع: {{ fa_digits($start->format('H:i')) }}</p>
    @endif
</div>
