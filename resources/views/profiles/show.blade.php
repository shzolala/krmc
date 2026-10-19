<x-layouts.site :title="$profileUser->name">
    <header class="mx-auto max-w-7xl px-4 pt-6 sm:px-6 lg:px-8 lg:pt-8">
        <x-site.breadcrumbs tone="dark" :items="[$profileUser->name => null]" />
        <div class="mt-3 flex flex-wrap items-center gap-3">
            <h1 class="text-2xl font-extrabold text-navy-900 sm:text-3xl">{{ $profileUser->name }}</h1>
            <span class="rounded-full bg-white px-3 py-1 text-xs font-semibold text-navy-800 ring-1 ring-navy-900/10" dir="ltr">{{ '@'.$profileUser->username }}</span>
            <x-membership-badge :user="$profileUser" class="!px-3 !py-1 !text-xs" />
        </div>
        <p class="mt-1 text-sm text-muted">{{ $profileUser->roleLabel() }} · پروفایل عمومی</p>
    </header>

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

        @if ($profileUser->shops->isNotEmpty())
            <x-site.panel :title="$profileUser->shops->count() > 1 ? 'فروشگاه‌ها' : 'فروشگاه'" icon="bag">
                <div class="grid gap-3 sm:grid-cols-2">
                    @foreach ($profileUser->shops as $shop)
                        <x-site.link-item :href="route('shops.show', $shop)" :title="$shop->name" :subtitle="$shop->summary" />
                    @endforeach
                </div>
            </x-site.panel>
        @endif

        @if ($profileUser->primaryClub && $profileUser->ownedClubs->isEmpty())
            <x-site.panel title="باشگاه اصلی" icon="users">
                <x-site.link-item :href="route('clubs.show', $profileUser->primaryClub)" :title="$profileUser->primaryClub->name" :image="$profileUser->primaryClub->cover_path" />
            </x-site.panel>
        @endif
    </x-site.detail-layout>
</x-layouts.site>
