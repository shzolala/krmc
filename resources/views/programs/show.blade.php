@php
    $remaining = $program->capacity ? max($program->capacity - $program->registered_count, 0) : null;
    $fill = $program->capacity ? min(100, round($program->registered_count / $program->capacity * 100)) : 0;
    $isOngoing = $program->status === \App\Enums\ProgramStatus::Ongoing;
    $duration = $program->starts_at && $program->ends_at ? (int) ceil($program->starts_at->diffInDays($program->ends_at, true)) + 1 : null;
@endphp

<x-layouts.site :title="$program->title" :meta-description="$program->summary">
    <x-site.detail-header
        :title="$program->title"
        :crumbs="['برنامه‌ها' => route('programs.index'), $program->title => null]"
        :kicker="$program->status->label()"
        :cover="$program->cover_path ?? $program->destination?->cover_path ?? \App\Support\CatalogCover::for('program-'.$program->slug)"
        :seed="'program-'.$program->slug"
        :facts="[
            ['value' => $program->starts_at ? fa_digits($program->starts_at->format('Y/m/d')) : null, 'label' => 'شروع'],
            ['value' => $duration ? fa_num($duration).' روز' : null, 'label' => 'مدت'],
            ['value' => $program->capacity ? fa_num($program->capacity).' نفر' : null, 'label' => 'ظرفیت'],
            ['value' => $remaining !== null ? fa_num($remaining) : null, 'label' => 'جای خالی'],
        ]"
    >
        <x-slot:meta>
            @if ($program->club)
                <a href="{{ route('clubs.show', $program->club) }}" class="inline-flex items-center gap-1.5 hover:text-accent"><x-site.icon name="users" class="h-4 w-4 text-accent" />{{ $program->club->name }}</a>
            @endif
            @if ($program->destination)
                <a href="{{ route('destinations.show', $program->destination) }}" class="inline-flex items-center gap-1.5 hover:text-accent"><x-site.icon name="mountain" class="h-4 w-4 text-accent" />{{ $program->destination->name }}</a>
            @endif
        </x-slot:meta>
    </x-site.detail-header>

    <x-site.detail-layout>
        <x-site.panel title="درباره برنامه" icon="flag">
            <p class="whitespace-pre-line text-sm leading-8 text-muted">{{ $program->description ?: 'توضیحات تکمیلی هنوز ثبت نشده است.' }}</p>
        </x-site.panel>

        @if ($program->destination)
            <x-site.panel title="مقصد برنامه" icon="mountain">
                <x-site.link-item
                    :href="route('destinations.show', $program->destination)"
                    :title="$program->destination->name"
                    :kicker="$program->destination->categoryLabel()"
                    :subtitle="$program->destination->mountainRegion?->name"
                    :image="$program->destination->cover_path"
                />
            </x-site.panel>
        @endif

        <x-slot:aside>
            <x-site.panel title="جزئیات برنامه" icon="calendar">
                @if ($program->capacity)
                    <div class="mb-4">
                        <div class="flex items-center justify-between text-xs">
                            <span class="font-semibold text-navy-900">{{ fa_num($remaining) }} جای خالی</span>
                            <span class="text-muted">{{ fa_num($program->registered_count) }} از {{ fa_num($program->capacity) }} نفر</span>
                        </div>
                        <div class="mt-2 h-2 overflow-hidden rounded-full bg-sand" role="progressbar" aria-valuenow="{{ $fill }}" aria-valuemin="0" aria-valuemax="100">
                            <div class="h-full rounded-full bg-accent" style="width: {{ $fill }}%"></div>
                        </div>
                    </div>
                @endif

                <x-site.facts :columns="1" :items="[
                    ['label' => 'زمان', 'value' => $program->starts_at ? fa_digits($program->starts_at->format('Y/m/d')).($program->ends_at ? ' — '.fa_digits($program->ends_at->format('Y/m/d')) : '') : null, 'icon' => 'calendar'],
                    ['label' => 'مقصد', 'value' => $program->destination?->name, 'icon' => 'mountain'],
                    ['label' => 'باشگاه برگزارکننده', 'value' => $program->club?->name, 'icon' => 'users'],
                    ['label' => 'سرپرست', 'value' => $program->leader?->user?->name, 'icon' => 'user'],
                ]" />

                @if ($program->leader)
                    <x-site.link-item class="mt-3" :href="route('coaches.show', $program->leader)" :title="$program->leader->user?->name" kicker="سرپرست برنامه" :subtitle="$program->leader->specialty" />
                @endif
            </x-site.panel>
        </x-slot:aside>
    </x-site.detail-layout>
</x-layouts.site>
