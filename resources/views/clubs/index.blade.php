<x-layouts.site title="باشگاه‌ها" meta-description="دایرکتوری باشگاه‌های رسمی کوهنوردی کرمان با اعضا، مربیان و برنامه‌ها.">
    <x-site.listing
        title="باشگاه‌های کوهنوردی کرمان"
        :action="route('clubs.index')"
        :paginator="$clubs"
        empty-message="باشگاهی با این مشخصات پیدا نشد."
        :filters="[
            ['type' => 'search', 'name' => 'q', 'label' => 'نام باشگاه، شهر یا مدیر', 'placeholder' => 'مثلاً اماوند...'],
            ['type' => 'select', 'name' => 'city', 'label' => 'شهر', 'all' => 'همه شهرها', 'options' => $cities->all()],
            ['type' => 'toggle', 'name' => 'verified', 'label' => 'فقط تأییدشده فدراسیون'],
            ['type' => 'toggle', 'name' => 'has_programs', 'label' => 'فقط باشگاه‌های دارای برنامه فعال'],
        ]"
    >
        @foreach ($clubs as $club)
            <x-cards.club :club="$club" />
        @endforeach
    </x-site.listing>
</x-layouts.site>
