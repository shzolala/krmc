@props([
    'grade' => null,
    'tone' => 'light',
])

@php
    $isDark = $tone === 'dark';
    $level = \App\Enums\DifficultyLevel::fromGrade($grade);
@endphp

@if ($grade)
    <span {{ $attributes->class(['inline-flex items-center gap-2']) }} title="درجه سختی {{ fa_num($grade) }} از ۵">
        <span class="flex items-center gap-0.5" aria-hidden="true">
            @foreach (range(1, 5) as $pip)
                <span @class([
                    'h-3 w-1.5 rounded-full',
                    'bg-accent' => $pip <= $grade,
                    'bg-white/20' => $pip > $grade && $isDark,
                    'bg-navy-900/15' => $pip > $grade && ! $isDark,
                ])></span>
            @endforeach
        </span>
        <span @class(['text-xs font-semibold', 'text-white/80' => $isDark, 'text-navy-800' => ! $isDark])>{{ $level?->label() }}</span>
    </span>
@endif
