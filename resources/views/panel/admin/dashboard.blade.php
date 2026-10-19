@php
    $maxSignup = max(1, collect($signups)->max('count'));
    $maxRole = max(1, $roleCounts->max('count'));
    $quickCreate = ['clubs', 'destinations', 'programs', 'attractions'];
@endphp

<x-layouts::admin title="داشبورد مدیریت">
    <x-admin.page-header
        title="سلام {{ auth()->user()->name }} 👋"
        :subtitle="'مرور وضعیت سایت — '.fa_date(now(), false, true)"
        icon="squares-2x2"
    >
        <flux:button :href="route('panel.admin.users.create')" icon="user-plus" wire:navigate>کاربر جدید</flux:button>
        <flux:dropdown align="end">
            <flux:button variant="primary" icon="plus" icon-trailing="chevron-down">افزودن محتوا</flux:button>
            <flux:menu>
                @foreach ($inventory as $item)
                    <flux:menu.item :icon="$item['resource']->icon()" :href="route('panel.admin.resources.create', $item['resource']->key())" wire:navigate>
                        {{ $item['resource']->singular() }}
                    </flux:menu.item>
                @endforeach
            </flux:menu>
        </flux:dropdown>
    </x-admin.page-header>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-admin.stat label="کاربران" :value="fa_num($stats['users'])" icon="users" tone="blue"
            :hint="fa_num($stats['new_users_week']).' عضو جدید در ۷ روز اخیر'" :href="route('panel.admin.users.index')" />
        <x-admin.stat label="باشگاه‌ها و مربیان" :value="fa_num($stats['clubs'] + $stats['coaches'])" icon="building-office-2" tone="green"
            :hint="fa_num($stats['clubs']).' باشگاه · '.fa_num($stats['coaches']).' مربی/سرپرست'" :href="route('panel.admin.resources.index', 'clubs')" />
        <x-admin.stat label="برنامه‌های فعال" :value="fa_num($stats['active_programs'])" icon="calendar-days" tone="purple"
            :hint="fa_num($stats['destinations']).' مقصد ثبت‌شده'" :href="route('panel.admin.resources.index', 'programs')" />
        <x-admin.stat label="نظرات در انتظار" :value="fa_num($stats['pending_reviews'])" icon="chat-bubble-left-right" tone="amber"
            :hint="$stats['pending_reviews'] > 0 ? 'نیازمند بررسی شما' : 'همه نظرات بررسی شده‌اند'" :href="route('panel.admin.reviews')" />
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="flex flex-col gap-6 lg:col-span-2">
            <x-admin.card title="ثبت‌نام کاربران" description="۱۴ روز گذشته">
                <div class="flex h-32 items-end gap-1.5" dir="ltr">
                    @foreach ($signups as $day)
                        <div class="group relative flex h-full flex-1 flex-col justify-end" title="{{ fa_date($day['date']) }}: {{ fa_num($day['count']) }}">
                            <div class="rounded-t bg-navy-700/80 transition group-hover:bg-brand dark:bg-brand/70"
                                style="height: {{ max(4, ($day['count'] / $maxSignup) * 100) }}%"></div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-2 flex justify-between text-[11px] text-zinc-500">
                    <span>{{ fa_date($signups[0]['date']) }}</span>
                    <span>{{ fa_date($signups[count($signups) - 1]['date']) }}</span>
                </div>
            </x-admin.card>

            <x-admin.card title="موجودی محتوا" description="مدیریت همه بخش‌های سایت" :padded="false">
                <div class="grid divide-y divide-zinc-100 sm:grid-cols-2 sm:divide-y-0 dark:divide-zinc-800">
                    @foreach ($inventory as $item)
                        @php $resource = $item['resource']; @endphp
                        <div class="flex items-center gap-3 border-zinc-100 px-5 py-3 sm:border-b sm:odd:border-e dark:border-zinc-800">
                            <flux:icon :name="$resource->icon()" class="size-5 shrink-0 text-zinc-400" />
                            <a href="{{ route('panel.admin.resources.index', $resource->key()) }}" wire:navigate class="min-w-0 flex-1 truncate text-sm font-medium hover:text-brand">
                                {{ $resource->plural() }}
                            </a>
                            <span class="text-sm font-bold tabular-nums">{{ fa_num($item['count']) }}</span>
                            <a href="{{ route('panel.admin.resources.create', $resource->key()) }}" wire:navigate
                                class="flex size-7 items-center justify-center rounded-md text-zinc-400 hover:bg-zinc-100 hover:text-zinc-900 dark:hover:bg-zinc-800 dark:hover:text-zinc-100"
                                title="افزودن {{ $resource->singular() }}">
                                <flux:icon.plus class="size-4" />
                            </a>
                        </div>
                    @endforeach
                </div>
            </x-admin.card>

            <div class="grid gap-6 md:grid-cols-2">
                <x-admin.card title="پربازدیدترین مقاصد" :padded="false">
                    <ul class="divide-y divide-zinc-100 dark:divide-zinc-800">
                        @forelse ($topDestinations as $index => $destination)
                            <li class="flex items-center gap-3 px-5 py-2.5 text-sm">
                                <span class="flex size-6 items-center justify-center rounded-full bg-zinc-100 text-xs font-bold dark:bg-zinc-800">{{ fa_num($index + 1) }}</span>
                                <a href="{{ route('panel.admin.resources.edit', ['resource' => 'destinations', 'id' => $destination->id]) }}" wire:navigate class="min-w-0 flex-1 truncate hover:text-brand">{{ $destination->name }}</a>
                                <span class="text-xs text-zinc-500">{{ fa_num($destination->view_count) }} بازدید</span>
                            </li>
                        @empty
                            <li class="px-5 py-6 text-center text-sm text-zinc-500">مقصدی ثبت نشده است.</li>
                        @endforelse
                    </ul>
                </x-admin.card>

                <x-admin.card title="برنامه‌های پیش‌رو" :padded="false">
                    <ul class="divide-y divide-zinc-100 dark:divide-zinc-800">
                        @forelse ($upcomingPrograms as $program)
                            <li class="flex items-center gap-3 px-5 py-2.5 text-sm">
                                <div class="min-w-0 flex-1">
                                    <a href="{{ route('panel.admin.resources.edit', ['resource' => 'programs', 'id' => $program->id]) }}" wire:navigate class="block truncate hover:text-brand">{{ $program->title }}</a>
                                    <span class="text-xs text-zinc-500">{{ $program->club?->name }}</span>
                                </div>
                                <span class="text-xs text-zinc-500">{{ fa_date($program->starts_at) }}</span>
                            </li>
                        @empty
                            <li class="px-5 py-6 text-center text-sm text-zinc-500">برنامه فعالی وجود ندارد.</li>
                        @endforelse
                    </ul>
                </x-admin.card>
            </div>
        </div>

        <div class="flex flex-col gap-6">
            <x-admin.card title="نیازمند توجه" :padded="false">
                <ul class="divide-y divide-zinc-100 dark:divide-zinc-800">
                    @forelse ($attention as $item)
                        <li>
                            <a href="{{ $item['url'] }}" wire:navigate class="flex items-center justify-between gap-3 px-5 py-3 text-sm hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                                <span>{{ $item['label'] }}</span>
                                <flux:badge size="sm" :color="$item['tone']">{{ fa_num($item['count']) }}</flux:badge>
                            </a>
                        </li>
                    @empty
                        <li class="flex items-center gap-2 px-5 py-6 text-sm text-emerald-600">
                            <flux:icon.check-badge class="size-5" /> همه‌چیز مرتب است.
                        </li>
                    @endforelse
                </ul>
            </x-admin.card>

            <x-admin.card title="کاربران به تفکیک نقش" :padded="false">
                <x-slot:actions>
                    <a href="{{ route('panel.admin.roles') }}" wire:navigate class="text-xs text-zinc-500 hover:text-brand">نقش‌ها و دسترسی‌ها</a>
                </x-slot:actions>
                <ul class="space-y-3 p-5">
                    @foreach ($roleCounts as $row)
                        <li>
                            <a href="{{ route('panel.admin.users.index', ['role' => $row['role']->value]) }}" wire:navigate class="group block">
                                <div class="mb-1 flex items-center justify-between text-xs">
                                    <span class="font-medium group-hover:text-brand">{{ $row['role']->label() }}</span>
                                    <span class="text-zinc-500">{{ fa_num($row['count']) }}</span>
                                </div>
                                <div class="h-1.5 overflow-hidden rounded-full bg-zinc-100 dark:bg-zinc-800">
                                    <div class="h-full rounded-full bg-navy-700 dark:bg-brand" style="width: {{ ($row['count'] / $maxRole) * 100 }}%"></div>
                                </div>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </x-admin.card>

            <x-admin.card title="جدیدترین کاربران" :padded="false">
                <x-slot:actions>
                    <a href="{{ route('panel.admin.users.index') }}" wire:navigate class="text-xs text-zinc-500 hover:text-brand">همه</a>
                </x-slot:actions>
                <ul class="divide-y divide-zinc-100 dark:divide-zinc-800">
                    @foreach ($recentUsers as $user)
                        <li>
                            <a href="{{ route('panel.admin.users.edit', $user) }}" wire:navigate class="flex items-center gap-3 px-5 py-2.5 hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                                <flux:avatar size="sm" :name="$user->name" :initials="$user->initials()" />
                                <span class="min-w-0 flex-1">
                                    <span class="block truncate text-sm font-medium">{{ $user->name }}</span>
                                    <span class="block text-xs text-zinc-500">{{ fa_date($user->created_at) }}</span>
                                </span>
                                <x-role-badges :user="$user" />
                            </a>
                        </li>
                    @endforeach
                </ul>
            </x-admin.card>

            @if ($pendingReviewList->isNotEmpty())
                <x-admin.card title="آخرین نظرات در انتظار" :padded="false">
                    <x-slot:actions>
                        <a href="{{ route('panel.admin.reviews') }}" wire:navigate class="text-xs text-zinc-500 hover:text-brand">بررسی</a>
                    </x-slot:actions>
                    <ul class="divide-y divide-zinc-100 dark:divide-zinc-800">
                        @foreach ($pendingReviewList as $review)
                            <li class="px-5 py-3 text-sm">
                                <div class="mb-0.5 flex items-center justify-between gap-2">
                                    <span class="font-medium">{{ $review->user?->name }}</span>
                                    <span class="text-xs text-amber-600">{{ str_repeat('★', $review->rating) }}</span>
                                </div>
                                <p class="line-clamp-2 text-xs text-zinc-500">{{ $review->body }}</p>
                            </li>
                        @endforeach
                    </ul>
                </x-admin.card>
            @endif
        </div>
    </div>
</x-layouts::admin>
