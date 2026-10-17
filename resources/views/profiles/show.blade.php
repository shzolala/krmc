<x-layouts.site :title="$profileUser->name">
    <x-site.page-hero
        :crumbs="[$profileUser->name => null]"
        :title="$profileUser->name"
        :description="$profileUser->roleLabel()"
    >
        <x-slot:badges>
            <span class="rounded-full bg-white/10 px-3 py-1 text-xs font-semibold ring-1 ring-white/20" dir="ltr">{{ '@'.$profileUser->username }}</span>
            <span class="rounded-full bg-white/10 px-3 py-1 text-xs font-semibold ring-1 ring-white/20">پروفایل عمومی</span>
        </x-slot:badges>
    </x-site.page-hero>

    <x-site.detail-layout>
        <x-site.panel title="اطلاعات پروفایل" icon="user">
            <x-site.facts :items="[
                ['label' => 'نام کاربری', 'value' => '@'.$profileUser->username],
                ['label' => 'نقش', 'value' => $profileUser->roleLabel()],
            ]" />
        </x-site.panel>

        @if ($profileUser->coachProfile)
            <x-site.panel title="پروفایل تخصصی" icon="academic">
                <p class="text-sm leading-7 text-muted">{{ $profileUser->coachProfile->headline }}</p>
                <p class="mt-2 text-sm text-navy-900">تخصص: {{ $profileUser->coachProfile->specialty }}</p>
                <x-site.button class="mt-4" :href="route('coaches.show', $profileUser->coachProfile)" variant="dark">مشاهده صفحه مربی/سرپرست</x-site.button>
            </x-site.panel>
        @endif

        @if ($profileUser->ownedClubs->isNotEmpty())
            <x-site.panel title="باشگاه‌ها" icon="users">
                <div class="grid gap-3 sm:grid-cols-2">
                    @foreach ($profileUser->ownedClubs as $club)
                        <x-site.link-item :href="route('clubs.show', $club)" :title="$club->name" :subtitle="$club->summary" :image="$club->cover_path" />
                    @endforeach
                </div>
            </x-site.panel>
        @endif

        @if ($profileUser->shop)
            <x-site.panel title="فروشگاه" icon="bag">
                <x-site.link-item :href="route('shops.show', $profileUser->shop)" :title="$profileUser->shop->name" :subtitle="$profileUser->shop->summary" />
            </x-site.panel>
        @endif

        @if ($profileUser->primaryClub && $profileUser->ownedClubs->isEmpty())
            <x-site.panel title="باشگاه اصلی" icon="users">
                <x-site.link-item :href="route('clubs.show', $profileUser->primaryClub)" :title="$profileUser->primaryClub->name" :image="$profileUser->primaryClub->cover_path" />
            </x-site.panel>
        @endif
    </x-site.detail-layout>
</x-layouts.site>
