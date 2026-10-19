<x-layouts.site title="برنامه‌ها" meta-description="برنامه‌های کوهنوردی و طبیعت‌گردی باشگاه‌های کرمان.">
    <x-site.listing
        title="برنامه‌های باشگاهی"
        :action="route('programs.index')"
        :paginator="$programs"
        empty-message="برنامه‌ای با این مشخصات ثبت نشده است."
        :filters="[
            ['type' => 'search', 'name' => 'q', 'label' => 'عنوان برنامه', 'placeholder' => 'مثلاً هزار...'],
            ['type' => 'options', 'name' => 'status', 'label' => 'وضعیت', 'all' => 'همه فعال', 'options' => ['ongoing' => 'در حال برگزاری', 'upcoming' => 'پیش‌رو', 'completed' => 'برگزارشده']],
            ['type' => 'options', 'name' => 'difficulty', 'label' => 'سطح سختی مقصد', 'options' => collect(\App\Enums\DifficultyLevel::cases())->mapWithKeys(fn ($level) => [$level->value => $level->label()])->all()],
            ['type' => 'select', 'name' => 'region', 'label' => 'منطقه', 'all' => 'همه مناطق', 'options' => $regions->pluck('name', 'slug')->all()],
            ['type' => 'select', 'name' => 'club', 'label' => 'باشگاه برگزارکننده', 'all' => 'همه باشگاه‌ها', 'options' => $clubs->pluck('name', 'slug')->all()],
            ['type' => 'toggle', 'name' => 'available', 'label' => 'فقط برنامه‌های دارای ظرفیت خالی'],
        ]"
    >
        @foreach ($programs as $program)
            <x-cards.program :program="$program" />
        @endforeach
    </x-site.listing>
</x-layouts.site>
