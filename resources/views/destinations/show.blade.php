@php
    $trail = $destination->trail_info ?? [];
    $bestSeason = $trail['best_season'] ?? null;
    $startPoint = $trail['start_point'] ?? null;
    $difficulty = $destination->difficultyGrade();
    $guides = $destination->guides;
@endphp

<x-layouts.site :title="$destination->name" :meta-description="$destination->summary">
    <x-site.detail-header
        :title="$destination->name"
        :crumbs="['مقاصد' => route('destinations.index'), $destination->name => null]"
        :kicker="$destination->categoryLabel()"
        :cover="$destination->cover_path"
        :seed="'destination-'.$destination->slug"
        :reviewable="$destination"
    >
        <x-slot:meta>
            @if ($destination->mountainRegion)
                <a href="{{ route('regions.show', $destination->mountainRegion) }}" class="inline-flex items-center gap-1.5 hover:text-accent">
                    <x-site.icon name="compass" class="h-4 w-4 text-accent" />
                    {{ $destination->mountainRegion->name }}
                </a>
            @elseif ($destination->region)
                <span class="inline-flex items-center gap-1.5"><x-site.icon name="pin" class="h-4 w-4 text-accent" />{{ $destination->region }}</span>
            @endif
            @if ($destination->elevation)
                <span class="inline-flex items-center gap-1.5"><x-site.icon name="mountain" class="h-4 w-4 text-accent" />{{ fa_num($destination->elevation) }} متر</span>
            @endif
        </x-slot:meta>

        <x-slot:actions>
            <x-site.button href="#programs" icon="calendar">برنامه‌ها</x-site.button>
            <x-site.button href="#reviews" variant="outline" icon="star">نظرات</x-site.button>
        </x-slot:actions>
    </x-site.detail-header>

    <x-site.detail-layout>
        <x-site.panel title="درباره مقصد" icon="mountain">
            <p class="whitespace-pre-line text-sm leading-8 text-muted">{{ $destination->description }}</p>
        </x-site.panel>

        @if (! empty($trail))
            <x-site.panel title="اطلاعات مسیر" icon="flag">
                <x-site.facts :items="[
                    ['label' => 'نقطه شروع', 'value' => is_array($startPoint) ? ($startPoint['name'] ?? null) : $startPoint, 'icon' => 'pin'],
                    ['label' => 'مدت معمول', 'value' => $trail['typical_duration'] ?? null, 'icon' => 'clock'],
                    ['label' => 'بهترین فصل', 'value' => is_array($bestSeason) ? implode('، ', $bestSeason) : $bestSeason, 'icon' => 'sun'],
                    ['label' => 'درجه سختی', 'value' => $difficulty ? fa_num($difficulty).' از ۵' : null, 'icon' => 'trend'],
                ]" />
            </x-site.panel>
        @endif

        <x-site.panel id="programs" title="برنامه‌های این مقصد" icon="calendar">
            <div class="grid gap-4 md:grid-cols-2">
                @forelse ($destination->programs as $program)
                    <x-cards.program :program="$program" class="!shadow-none" />
                @empty
                    <p class="text-sm text-muted md:col-span-2">در حال حاضر برنامه فعالی برای این مقصد ثبت نشده است.</p>
                @endforelse
            </div>
        </x-site.panel>

        @if ($destination->programReports->isNotEmpty())
            <x-site.panel title="گزارش برنامه‌ها" icon="document">
                <div class="grid gap-3">
                    @foreach ($destination->programReports as $report)
                        <div class="rounded-2xl bg-sand p-4">
                            <p class="text-sm font-bold text-navy-900">{{ $report->title }}</p>
                            <p class="mt-1 line-clamp-2 text-xs leading-6 text-muted">{{ $report->excerpt }}</p>
                        </div>
                    @endforeach
                </div>
            </x-site.panel>
        @endif

        <x-slot:aside>
            <x-site.map-panel :markers="$mapMarkers" :zoom="11" />

            @if ($destination->nearest_city || $destination->address || $destination->county)
                <x-site.panel title="موقعیت" icon="pin">
                    <x-site.facts :columns="1" :items="[
                        ['label' => 'نزدیک‌ترین شهر', 'value' => $destination->nearest_city],
                        ['label' => 'شهرستان', 'value' => $destination->county],
                        ['label' => 'آدرس', 'value' => $destination->address],
                    ]" />
                </x-site.panel>
            @endif

            <x-site.panel title="چه کسانی این مقصد را می‌برند؟" icon="users">
                <div class="space-y-2.5">
                    @forelse ($guides as $guide)
                        @if ($guide->club)
                            <x-site.link-item :href="route('clubs.show', $guide->club)" :title="$guide->club->name" kicker="باشگاه" :image="$guide->club->cover_path" />
                        @endif
                        @if ($guide->coachProfile)
                            <x-site.link-item :href="route('coaches.show', $guide->coachProfile)" :title="$guide->coachProfile->user->name" :kicker="$guide->coachProfile->roleLabel()" />
                        @endif
                    @empty
                        <p class="text-sm text-muted">هنوز راهنمایی برای این مقصد ثبت نشده است.</p>
                    @endforelse
                </div>
            </x-site.panel>

            @if ($destination->shelters->isNotEmpty())
                <x-site.panel title="پناهگاه‌های مسیر" icon="shelter" tone="warm">
                    <div class="space-y-2.5">
                        @foreach ($destination->shelters as $shelter)
                            <x-site.link-item :href="route('shelters.show', $shelter)" :title="$shelter->name" subtitle="پناهگاه کوهستانی · نه اقامتگاه تجاری" :image="$shelter->cover_path" />
                        @endforeach
                    </div>
                </x-site.panel>
            @endif
        </x-slot:aside>
    </x-site.detail-layout>

    @if ($destination->relatedDestinations->isNotEmpty())
        <x-site.section eyebrow="در همین منطقه" title="قله‌ها و نقاط نزدیک" spacing="pt-14">
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($destination->relatedDestinations as $related)
                    <x-cards.destination :destination="$related" />
                @endforeach
            </div>
        </x-site.section>
    @endif

    @if ($destination->attractions->isNotEmpty())
        <x-site.section eyebrow="گردشگری" title="جاذبه‌های نزدیک" spacing="pt-14">
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($destination->attractions as $attraction)
                    <x-cards.attraction :attraction="$attraction" />
                @endforeach
            </div>
        </x-site.section>
    @endif

    @if ($destination->accommodations->isNotEmpty())
        <x-site.section eyebrow="اقامت" title="اقامتگاه‌های نزدیک" spacing="pt-14">
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($destination->accommodations as $accommodation)
                    <x-cards.accommodation :accommodation="$accommodation" />
                @endforeach
            </div>
        </x-site.section>
    @endif

    <x-site.section spacing="pt-14">
        <livewire:review.section :reviewable="$destination" :key="'destination-reviews-'.$destination->id" />
    </x-site.section>
</x-layouts.site>
