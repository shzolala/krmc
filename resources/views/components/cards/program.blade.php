@props([
    'program',
    'showCapacity' => true,
])

@php
    $remaining = $program->capacity ? max($program->capacity - $program->registered_count, 0) : null;
    $fill = $program->capacity ? min(100, round($program->registered_count / $program->capacity * 100)) : 0;
    $isOngoing = $program->status === \App\Enums\ProgramStatus::Ongoing;
@endphp

<x-site.media-card
    :href="route('programs.show', $program)"
    :title="$program->title"
    :image="$program->cover_path ?? $program->destination?->cover_path ?? \App\Support\CatalogCover::for('program-'.$program->slug)"
    :kicker="collect([$program->destination?->name, $program->club?->name])->filter()->implode(' · ')"
    image-height="h-48"
    {{ $attributes }}
>
    <x-slot:badges>
        <span @class([
            'inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-bold',
            'bg-emerald-500 text-white' => $isOngoing,
            'bg-accent text-navy-950' => ! $isOngoing,
        ])>
            @if ($isOngoing)
                <span class="h-1.5 w-1.5 animate-pulse rounded-full bg-white"></span>
            @endif
            {{ $program->status->label() }}
        </span>
    </x-slot:badges>

    <div class="mt-auto pt-5">
        @if ($grade = $program->destination?->difficultyGrade())
            <x-site.difficulty-meter :grade="$grade" class="mb-3" />
        @endif
        @if (! $showCapacity)
            @if ($program->registered_count > 0)
                <p class="flex items-center gap-1.5 text-xs font-semibold text-navy-900">
                    <x-site.icon name="users" class="h-4 w-4 text-accent" />
                    {{ fa_num($program->registered_count) }} نفر ثبت‌نام کرده‌اند
                </p>
            @endif
        @elseif ($program->capacity)
            <div class="flex items-center justify-between text-xs">
                <span class="font-semibold text-navy-900">{{ fa_num($remaining) }} جای خالی</span>
                <span class="text-muted">از {{ fa_num($program->capacity) }} نفر</span>
            </div>
            <div class="mt-2 h-1.5 overflow-hidden rounded-full bg-sand" role="progressbar" aria-valuenow="{{ $fill }}" aria-valuemin="0" aria-valuemax="100">
                <div class="h-full rounded-full bg-accent" style="width: {{ $fill }}%"></div>
            </div>
        @endif
        @if ($program->starts_at)
            <p class="mt-3 text-xs text-muted">
                @if ($program->starts_at->isFuture())
                    شروع: {{ fa_num((int) ceil(now()->diffInDays($program->starts_at, true))) }} روز دیگر
                @else
                    در جریان از {{ fa_num((int) ceil($program->starts_at->diffInDays(now(), true))) }} روز پیش
                @endif
                · {{ fa_date_range($program->starts_at, $program->ends_at) }}
            </p>
        @endif
    </div>
</x-site.media-card>
