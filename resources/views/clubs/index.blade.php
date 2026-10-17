<x-layouts.site title="باشگاه‌ها" meta-description="دایرکتوری باشگاه‌های رسمی کوهنوردی کرمان با اعضا، مربیان و برنامه‌ها.">
    <x-site.page-hero
        section="clubs"
        overlap
        :crumbs="['باشگاه‌ها' => null]"
        eyebrow="باشگاه‌های رسمی"
        title="باشگاه‌های کوهنوردی کرمان"
        :description="fa_num($clubs->total()).' باشگاه در دایرکتوری — با پروفایل، اعضا و برنامه‌ها.'"
    />

    <x-site.toolbar
        :action="route('clubs.index')"
        placeholder="نام باشگاه، شهر یا مدیر..."
        :query="$search"
        :hidden="['verified' => $onlyVerified ? 1 : null]"
        :total="$clubs->total()"
        :filters="[
            ['label' => 'همه', 'href' => route('clubs.index', array_filter(['q' => $search])), 'active' => ! $onlyVerified],
            ['label' => 'تأییدشده فدراسیون', 'href' => route('clubs.index', array_filter(['q' => $search, 'verified' => 1])), 'active' => $onlyVerified],
        ]"
    />

    <x-site.section spacing="pt-10">
        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
            @forelse ($clubs as $club)
                <x-cards.club :club="$club" />
            @empty
                <x-site.empty class="col-span-full" message="باشگاهی با این مشخصات پیدا نشد." :href="route('clubs.index')" />
            @endforelse
        </div>

        <div class="mt-10">{{ $clubs->links() }}</div>
    </x-site.section>
</x-layouts.site>
