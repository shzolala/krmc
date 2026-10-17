<x-layouts.site title="برنامه‌ها" meta-description="برنامه‌های کوهنوردی و طبیعت‌گردی باشگاه‌های کرمان.">
    <x-site.page-hero
        section="programs"
        overlap
        :crumbs="['برنامه‌ها' => null]"
        eyebrow="صعودها و تورها"
        title="برنامه‌های باشگاهی"
        :description="fa_num($programs->total()).' برنامه — هر برنامه به یک قله یا نقطه طبیعی وصل است و باشگاه برگزارکننده مشخص دارد.'"
    />

    <x-site.toolbar
        :action="route('programs.index')"
        placeholder="عنوان برنامه..."
        :query="$search"
        :hidden="['status' => $activeStatus]"
        :total="$programs->total()"
        :filters="collect(['' => 'همه فعال', 'ongoing' => 'در حال برگزاری', 'upcoming' => 'پیش‌رو'])->map(fn ($label, $status) => [
            'label' => $label,
            'href' => route('programs.index', array_filter(['q' => $search, 'status' => $status])),
            'active' => (string) $activeStatus === (string) $status,
        ])->values()->all()"
    />

    <x-site.section spacing="pt-10">
        <div class="grid gap-5 md:grid-cols-2 lg:grid-cols-3">
            @forelse ($programs as $program)
                <x-cards.program :program="$program" />
            @empty
                <x-site.empty class="col-span-full" message="برنامه‌ای با این مشخصات ثبت نشده است." :href="route('programs.index')" />
            @endforelse
        </div>

        <div class="mt-10">{{ $programs->links() }}</div>
    </x-site.section>
</x-layouts.site>
