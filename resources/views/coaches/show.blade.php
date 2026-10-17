<x-layouts.site :title="$coach->user->name" :meta-description="$coach->headline">
    <x-site.detail-header
        :title="$coach->user->name"
        :crumbs="['مربیان و سرپرستان' => route('coaches.index'), $coach->user->name => null]"
        :kicker="$coach->roleLabel()"
        :cover="\App\Support\CatalogCover::for('coach-'.$coach->slug)"
        :seed="'coach-'.$coach->slug"
        :reviewable="$coach"
        :facts="[
            ['value' => $coach->experience_years ? fa_num($coach->experience_years).' سال' : null, 'label' => 'سابقه'],
            ['value' => $coach->total_courses_taught ? fa_num($coach->total_courses_taught) : null, 'label' => 'دوره برگزار شده'],
            ['value' => $coach->specialty, 'label' => 'تخصص'],
        ]"
    >
        <x-slot:badges>
            @if ($coach->is_verified)<x-badge tone="verified">تأییدشده</x-badge>@endif
            @if ($coach->is_premium)<x-badge tone="premium">ویژه</x-badge>@endif
        </x-slot:badges>
        <x-slot:meta>
            @if ($coach->club)
                <a href="{{ route('clubs.show', $coach->club) }}" class="inline-flex items-center gap-1.5 hover:text-accent">
                    <x-site.icon name="users" class="h-4 w-4 text-accent" />{{ $coach->club->name }}
                </a>
            @endif
        </x-slot:meta>
    </x-site.detail-header>

    <x-site.detail-layout>
        <x-site.panel title="درباره" icon="user">
            <div class="flex items-start gap-4">
                <x-site.avatar :name="$coach->user->name" :image="$coach->avatar_path" size="lg" />
                <p class="whitespace-pre-line text-sm leading-8 text-muted">{{ $coach->bio }}</p>
            </div>
        </x-site.panel>

        <x-site.panel title="برنامه‌های {{ $coach->is_leader ? 'سرپرستی' : 'مرتبط' }}" icon="calendar">
            <div class="grid gap-4 md:grid-cols-2">
                @forelse ($coach->ledPrograms as $program)
                    <x-cards.program :program="$program" class="!shadow-none" />
                @empty
                    <p class="text-sm text-muted md:col-span-2">هنوز برنامه‌ای ثبت نشده است.</p>
                @endforelse
            </div>
        </x-site.panel>

        <x-slot:aside>
            <x-site.panel title="اطلاعات حرفه‌ای" icon="academic">
                <x-site.facts :columns="1" :items="[
                    ['label' => 'نقش', 'value' => $coach->roleLabel()],
                    ['label' => 'تخصص', 'value' => $coach->specialty],
                    ['label' => 'سابقه', 'value' => $coach->experience_years ? fa_num($coach->experience_years).' سال' : null],
                    ['label' => 'باشگاه', 'value' => $coach->club?->name],
                ]" />
            </x-site.panel>

            <x-site.panel title="مقاصدی که می‌برد" icon="mountain">
                <div class="space-y-2.5">
                    @forelse ($coach->destinationGuides as $guide)
                        <x-site.link-item :href="route('destinations.show', $guide->destination)" :title="$guide->destination->name" :image="$guide->destination->cover_path" />
                    @empty
                        <p class="text-sm text-muted">هنوز مقصدی ثبت نشده است.</p>
                    @endforelse
                </div>
            </x-site.panel>
        </x-slot:aside>
    </x-site.detail-layout>

    <x-site.section spacing="pt-14">
        <livewire:review.section :reviewable="$coach" :key="'coach-reviews-'.$coach->id" />
    </x-site.section>
</x-layouts.site>
