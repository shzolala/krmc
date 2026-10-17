@props([
    'club',
])

@php
    $activeProgram = $club->relationLoaded('programs') ? $club->programs->first() : null;
@endphp

<x-site.media-card
    :href="route('clubs.show', $club)"
    :reviewable="$club"
    :title="$club->name"
    :image="$club->cover_path"
    :corner="$club->city"
    {{ $attributes }}
>
    <x-slot:badges>
        @if ($club->is_premium)
            <span class="rounded-full bg-accent px-2.5 py-0.5 text-[11px] font-bold text-navy-950">همکار ویژه</span>
        @elseif ($club->is_featured)
            <span class="rounded-full bg-sky-400 px-2.5 py-0.5 text-[11px] font-bold text-navy-950">برگزیده</span>
        @endif
        @if ($club->is_verified)
            <span class="rounded-full bg-emerald-500 px-2.5 py-0.5 text-[11px] font-semibold text-white" title="تأییدشده توسط فدراسیون">رسمی</span>
        @endif
    </x-slot:badges>

    <div class="mt-3 flex flex-wrap gap-1.5">
        @foreach ($club->topSpecialties(2) as $specialty)
            <span class="rounded-md bg-sand px-2 py-0.5 text-[11px] font-medium text-muted">{{ $specialty }}</span>
        @endforeach
    </div>

    @if ($activeProgram)
        <p class="mt-3 flex items-center gap-2 truncate rounded-lg bg-accent/10 px-2.5 py-1.5 text-xs font-medium text-navy-900">
            <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-accent"></span>
            <span class="truncate">{{ $activeProgram->title }}</span>
        </p>
    @endif

    <x-slot:footer>
        <span><b class="text-navy-900">{{ fa_num($club->member_count) }}</b> عضو</span>
        <span><b class="text-navy-900">{{ fa_num($club->program_count) }}</b> برنامه</span>
    </x-slot:footer>
</x-site.media-card>
