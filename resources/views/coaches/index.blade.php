<x-layouts.site title="مربیان و سرپرستان" meta-description="مربیان و سرپرستان معتبر کوهنوردی کرمان با تخصص، سابقه و تأییدیه مشخص.">
    <x-site.page-hero
        section="coaches"
        overlap
        :crumbs="['مربیان و سرپرستان' => null]"
        eyebrow="همراهان مسیر"
        title="مربیان و سرپرستان"
        :description="fa_num($coaches->total()).' پروفایل تخصصی با تأییدیه و سوابق مشخص.'"
    />

    <x-site.toolbar
        :action="route('coaches.index')"
        placeholder="نام، تخصص یا عنوان..."
        :query="$search"
        :hidden="['role' => $activeRole]"
        :total="$coaches->total()"
        :filters="collect(['' => 'همه', 'leader' => 'سرپرستان', 'coach' => 'مربیان'])->map(fn ($label, $role) => [
            'label' => $label,
            'href' => route('coaches.index', array_filter(['q' => $search, 'role' => $role])),
            'active' => $activeRole === $role,
        ])->values()->all()"
    />

    <x-site.section spacing="pt-10">
        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
            @forelse ($coaches as $coach)
                <x-cards.coach :coach="$coach" />
            @empty
                <x-site.empty class="col-span-full" message="پروفایلی با این مشخصات پیدا نشد." :href="route('coaches.index')" />
            @endforelse
        </div>

        <div class="mt-10">{{ $coaches->links() }}</div>
    </x-site.section>
</x-layouts.site>
