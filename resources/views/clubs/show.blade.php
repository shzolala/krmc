<x-layouts.site :title="$club->name" :meta-description="$club->summary">
    <x-site.detail-header
        :title="$club->name"
        :crumbs="['باشگاه‌ها' => route('clubs.index'), $club->name => null]"
        :cover="$club->cover_path"
        :seed="'club-'.$club->slug"
        :reviewable="$club"
        :facts="[
            ['value' => fa_num($club->member_count), 'label' => 'عضو'],
            ['value' => fa_num($club->program_count), 'label' => 'برنامه'],
            ['value' => fa_num($club->coachProfiles->count()), 'label' => 'مربی و سرپرست'],
            ['value' => $club->founded_year ? fa_digits($club->founded_year) : null, 'label' => 'سال تأسیس'],
        ]"
    >
        <x-slot:badges>
            @if ($club->is_premium)<x-badge tone="premium">همکار ویژه</x-badge>@endif
            @if ($club->is_featured)<x-badge tone="featured">ویژه</x-badge>@endif
            @if ($club->is_verified)<x-badge tone="verified">تأییدشده فدراسیون</x-badge>@endif
            <x-membership-badge :user="$club->owner" on-dark />
        </x-slot:badges>
        <x-slot:meta>
            @if ($club->city)
                <span class="inline-flex items-center gap-1.5"><x-site.icon name="pin" class="h-4 w-4 text-accent" />{{ $club->city }}</span>
            @endif
        </x-slot:meta>
        <x-slot:actions>
            @if ($club->programs->isNotEmpty())
                <x-site.button href="#programs" icon="calendar">برنامه‌ها</x-site.button>
            @endif
            <x-site.button href="#coaches" variant="outline" icon="academic">مربیان</x-site.button>
        </x-slot:actions>
    </x-site.detail-header>

    <x-site.detail-layout>
        <x-site.panel title="درباره باشگاه" icon="users">
            <p class="whitespace-pre-line text-sm leading-8 text-muted">{{ $club->description }}</p>
            <div class="mt-5 flex flex-wrap gap-1.5">
                @foreach ($club->topSpecialties(6) as $specialty)
                    <span class="rounded-full bg-sand px-3 py-1 text-xs font-medium text-navy-800">{{ $specialty }}</span>
                @endforeach
            </div>
        </x-site.panel>

        @if ($club->programs->isNotEmpty())
            <x-site.panel id="programs" title="برنامه‌های فعال" icon="calendar">
                <div class="grid gap-4 md:grid-cols-2">
                    @foreach ($club->programs as $program)
                        <x-cards.program :program="$program" class="!shadow-none" />
                    @endforeach
                </div>
            </x-site.panel>
        @endif

        @if ($club->coachProfiles->isNotEmpty())
            <x-site.panel id="coaches" title="مربیان و سرپرستان" icon="academic">
                <div class="grid gap-3 md:grid-cols-2">
                    @foreach ($club->coachProfiles as $coach)
                        <x-site.link-item
                            :href="route('coaches.show', $coach)"
                            :title="$coach->user->name"
                            :kicker="$coach->roleLabel()"
                            :subtitle="$coach->specialty"
                        />
                    @endforeach
                </div>
            </x-site.panel>
        @endif

        <x-slot:aside>
            <x-site.panel title="اطلاعات باشگاه" icon="document">
                <x-site.facts :columns="1" :items="[
                    ['label' => 'مدیر', 'value' => $club->manager, 'icon' => 'user'],
                    ['label' => 'مسئول آموزش', 'value' => $club->education_head, 'icon' => 'academic'],
                    ['label' => 'شهر', 'value' => $club->city, 'icon' => 'pin'],
                    ['label' => 'آدرس', 'value' => $club->address, 'icon' => 'pin'],
                ]" />
            </x-site.panel>

            @if ($club->latitude && $club->longitude)
                <x-site.map-panel :markers="[$club->toMapMarker('club', route('clubs.show', $club))]" :zoom="13" height="h-64" title="موقعیت باشگاه" />
            @endif

            <x-site.panel title="گزارش برنامه‌ها" icon="flag">
                <ul class="space-y-3">
                    @forelse ($club->programReports as $report)
                        <li class="rounded-2xl bg-sand p-3.5">
                            <p class="text-sm font-bold text-navy-900">{{ $report->title }}</p>
                            <p class="mt-1 text-xs text-muted">{{ fa_digits($report->published_at?->format('Y/m/d')) }}</p>
                        </li>
                    @empty
                        <li class="text-sm text-muted">هنوز گزارشی ثبت نشده است.</li>
                    @endforelse
                </ul>
            </x-site.panel>
        </x-slot:aside>
    </x-site.detail-layout>

    <x-site.section spacing="pt-14">
        <livewire:review.section :reviewable="$club" :key="'club-reviews-'.$club->id" />
    </x-site.section>
</x-layouts.site>
