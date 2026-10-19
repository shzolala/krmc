<x-layouts.site title="مربیان و سرپرستان" meta-description="مربیان و سرپرستان معتبر کوهنوردی کرمان با تخصص، سابقه و تأییدیه مشخص.">
    <x-site.listing
        title="مربیان و سرپرستان"
        :action="route('coaches.index')"
        :paginator="$coaches"
        grid="md:grid-cols-2 xl:grid-cols-3"
        empty-message="پروفایلی با این مشخصات پیدا نشد."
        :filters="[
            ['type' => 'search', 'name' => 'q', 'label' => 'نام، تخصص یا عنوان', 'placeholder' => 'مثلاً صعود فنی...'],
            ['type' => 'options', 'name' => 'role', 'label' => 'نقش', 'options' => ['leader' => 'سرپرست', 'coach' => 'مربی']],
            ['type' => 'select', 'name' => 'club', 'label' => 'باشگاه', 'all' => 'همه باشگاه‌ها', 'options' => $clubs->all()],
            ['type' => 'toggle', 'name' => 'verified', 'label' => 'فقط تأییدشده'],
        ]"
    >
        @foreach ($coaches as $coach)
            <x-cards.coach :coach="$coach" />
        @endforeach
    </x-site.listing>
</x-layouts.site>
