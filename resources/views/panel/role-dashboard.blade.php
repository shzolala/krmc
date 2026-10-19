@php
    use App\Enums\UserRole;

    $d = $dashboard;
    $completeness = $d['completeness'];
    $percent = $completeness['percent'];
    $ringColor = $percent >= 80 ? '#10b981' : ($percent >= 50 ? '#f59e0b' : '#ef4444');
    $greeting = match (true) {
        now()->hour < 12 => 'صبح بخیر',
        now()->hour < 18 => 'روز بخیر',
        default => 'شب بخیر',
    };
    $hasPrograms = $d['programs']->isNotEmpty();
    $showProgramsCard = ! in_array($role, [UserRole::Shop], true);
    $reviewsAreOwn = $role === UserRole::Climber;
@endphp

<x-layouts::app :title="$panel['label'] ?? 'داشبورد'">
    {{-- Hero --}}
    <section class="relative overflow-hidden rounded-2xl bg-gradient-to-l from-navy-950 via-navy-900 to-navy-700 p-6 text-white shadow-sm sm:p-8">
        <div class="pointer-events-none absolute -start-16 -top-16 size-64 rounded-full bg-brand/15 blur-3xl"></div>
        <div class="pointer-events-none absolute -bottom-24 end-10 size-72 rounded-full bg-white/5 blur-3xl"></div>

        <div class="relative flex flex-wrap items-center justify-between gap-6">
            <div class="flex items-center gap-4">
                <span class="flex size-16 shrink-0 items-center justify-center rounded-2xl bg-white/10 text-2xl font-extrabold text-brand-soft ring-1 ring-white/20">
                    {{ $user->initials() }}
                </span>
                <div class="min-w-0">
                    <p class="text-sm text-white/70">{{ $greeting }}،</p>
                    <h1 class="truncate text-2xl font-extrabold sm:text-3xl">{{ $user->name }}</h1>
                    <div class="mt-2 flex flex-wrap items-center gap-2 text-xs">
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-white/10 px-3 py-1 ring-1 ring-white/15">
                            <flux:icon :name="$role->icon()" class="size-3.5" /> {{ $role->label() }}
                        </span>
                        @if ($d['entityLabel'] && $d['entityLabel'] !== $role->label())
                            <span class="text-white/80">{{ $d['entityLabel'] }}</span>
                        @endif
                        <x-membership-badge :user="$user" on-dark class="!px-3 !py-1 !text-xs" />
                        @foreach ($d['badges'] as $badge)
                            <flux:badge size="sm" :color="$badge['color']">{{ $badge['label'] }}</flux:badge>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                @if ($d['editRoute'])
                    <a href="{{ $d['editRoute'] }}" wire:navigate class="inline-flex items-center gap-2 rounded-xl bg-brand px-4 py-2.5 text-sm font-bold text-navy-950 transition hover:bg-brand-soft">
                        <flux:icon.pencil-square class="size-4" /> ویرایش اطلاعات
                    </a>
                @endif
                @if ($d['publicUrl'])
                    <a href="{{ $d['publicUrl'] }}" target="_blank" class="inline-flex items-center gap-2 rounded-xl bg-white/10 px-4 py-2.5 text-sm font-medium ring-1 ring-white/20 transition hover:bg-white/20">
                        <flux:icon.arrow-top-right-on-square class="size-4" /> صفحه عمومی
                    </a>
                @endif
            </div>
        </div>
    </section>

    @if ($d['siblings'] || $user->roles()->count() > 1)
        <nav class="-mt-2 flex flex-wrap items-center gap-2 text-xs" aria-label="جابه‌جایی بین بخش‌ها">
            @if ($user->roles()->count() > 1)
                <a href="{{ route('panel.home') }}" wire:navigate class="inline-flex items-center gap-1.5 rounded-full border border-zinc-200 bg-white px-3 py-1.5 font-medium text-zinc-600 transition hover:border-brand hover:text-brand dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-300">
                    <flux:icon.squares-2x2 class="size-3.5" /> همه بخش‌های من
                </a>
            @endif
            @foreach ($d['siblings'] as $sibling)
                <a href="{{ $sibling['href'] }}" wire:navigate @class([
                    'inline-flex items-center gap-1.5 rounded-full border px-3 py-1.5 font-medium transition',
                    'border-navy-900 bg-navy-900 text-white dark:border-brand dark:bg-brand dark:text-zinc-900' => $sibling['current'],
                    'border-zinc-200 bg-white text-zinc-600 hover:border-brand hover:text-brand dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-300' => ! $sibling['current'],
                ])>
                    <flux:icon :name="$role->icon()" class="size-3.5" /> {{ $sibling['label'] }}
                </a>
            @endforeach
        </nav>
    @endif

    {{-- Stats --}}
    @if ($d['stats'])
        <div @class(['grid gap-4 sm:grid-cols-2', 'xl:grid-cols-4' => count($d['stats']) === 4, 'xl:grid-cols-3' => count($d['stats']) !== 4])>
            @foreach ($d['stats'] as $stat)
                <x-admin.stat :label="$stat['label']" :value="$stat['value']" :icon="$stat['icon']" :tone="$stat['tone']" :hint="$stat['hint']" :href="$stat['href']" />
            @endforeach
        </div>
    @endif

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="flex flex-col gap-6 lg:col-span-2">
            @if ($showProgramsCard)
                <x-admin.card :title="$d['programsTitle']" :padded="false">
                    <x-slot:actions>
                        <a href="{{ route('programs.index') }}" target="_blank" class="text-xs text-zinc-500 hover:text-brand">همه برنامه‌ها</a>
                    </x-slot:actions>
                    <ul class="divide-y divide-zinc-100 dark:divide-zinc-800">
                        @forelse ($d['programs'] as $program)
                            <li>
                                <a href="{{ route('programs.show', $program) }}" target="_blank" class="flex items-center gap-4 px-5 py-3.5 transition hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                                    <span class="flex size-12 shrink-0 flex-col items-center justify-center rounded-xl bg-navy-900 text-white dark:bg-zinc-800">
                                        <span class="text-[10px] leading-none text-white/60">{{ $program->starts_at ? explode(' ', fa_date($program->starts_at, false, true))[1] ?? '' : '' }}</span>
                                        <span class="text-base font-extrabold leading-tight">{{ $program->starts_at ? explode(' ', fa_date($program->starts_at, false, true))[0] : '—' }}</span>
                                    </span>
                                    <span class="min-w-0 flex-1">
                                        <span class="block truncate text-sm font-semibold">{{ $program->title }}</span>
                                        <span class="block truncate text-xs text-zinc-500">
                                            {{ $program->destination?->name }}
                                            @if ($program->club && $role !== UserRole::Club) · {{ $program->club->name }} @endif
                                        </span>
                                    </span>
                                    <span class="hidden text-xs text-zinc-500 sm:block">
                                        {{ fa_num($program->registered_count) }}@if ($program->capacity)/{{ fa_num($program->capacity) }}@endif نفر
                                    </span>
                                    <flux:badge size="sm" :color="match ($program->status->value) { 'ongoing' => 'green', 'completed' => 'zinc', 'cancelled' => 'red', default => 'blue' }">
                                        {{ $program->status->label() }}
                                    </flux:badge>
                                </a>
                            </li>
                        @empty
                            <li class="px-5 py-10 text-center text-sm text-zinc-500">
                                <flux:icon.calendar-days class="mx-auto mb-2 size-9 text-zinc-300" />
                                برنامه‌ای برای نمایش وجود ندارد.
                            </li>
                        @endforelse
                    </ul>
                </x-admin.card>
            @endif

            @if ($d['entity'] || $reviewsAreOwn)
                <x-admin.card :title="$d['reviewsTitle']" :padded="false">
                    @if ($d['pendingReviews'] > 0)
                        <x-slot:actions>
                            <flux:badge size="sm" color="amber">{{ fa_num($d['pendingReviews']) }} نظر در انتظار تأیید مدیریت</flux:badge>
                        </x-slot:actions>
                    @endif
                    <ul class="divide-y divide-zinc-100 dark:divide-zinc-800">
                        @forelse ($d['reviews'] as $review)
                            <li class="px-5 py-3.5">
                                <div class="mb-1 flex flex-wrap items-center justify-between gap-2">
                                    <span class="text-sm font-semibold">
                                        @if ($reviewsAreOwn)
                                            {{ $review->reviewable?->name ?? $review->reviewable?->title ?? 'مورد حذف‌شده' }}
                                        @else
                                            {{ $review->user?->name }}
                                        @endif
                                    </span>
                                    <span class="flex items-center gap-2">
                                        <span class="text-sm tracking-tight text-amber-500">{{ str_repeat('★', $review->rating) }}<span class="text-zinc-300 dark:text-zinc-600">{{ str_repeat('★', 5 - $review->rating) }}</span></span>
                                        @if ($reviewsAreOwn)
                                            <flux:badge size="sm" :color="match ($review->status->value) { 'approved' => 'green', 'rejected' => 'red', default => 'amber' }">{{ $review->status->label() }}</flux:badge>
                                        @endif
                                    </span>
                                </div>
                                <p class="line-clamp-2 text-sm leading-7 text-zinc-600 dark:text-zinc-400">{{ $review->body }}</p>
                                <span class="mt-1 block text-xs text-zinc-400">{{ fa_date($review->created_at) }}</span>
                            </li>
                        @empty
                            <li class="px-5 py-10 text-center text-sm text-zinc-500">
                                <flux:icon.chat-bubble-left-right class="mx-auto mb-2 size-9 text-zinc-300" />
                                {{ $reviewsAreOwn ? 'هنوز نظری ثبت نکرده‌اید؛ از صفحه هر مقصد می‌توانید نظر بدهید.' : 'هنوز نظر تأییدشده‌ای ثبت نشده است.' }}
                            </li>
                        @endforelse
                    </ul>
                </x-admin.card>
            @endif

            @if ($d['suggestions']->isNotEmpty())
                <x-admin.card title="پیشنهاد برای کاوش" description="مقاصد ویژه و پربازدید" :padded="false">
                    <div class="grid gap-px bg-zinc-100 sm:grid-cols-2 dark:bg-zinc-800">
                        @foreach ($d['suggestions'] as $destination)
                            <a href="{{ route('destinations.show', $destination) }}" target="_blank" class="flex items-center gap-3 bg-white px-5 py-3.5 transition hover:bg-zinc-50 dark:bg-zinc-900 dark:hover:bg-zinc-800/60">
                                <span class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">
                                    <flux:icon.flag class="size-5" />
                                </span>
                                <span class="min-w-0">
                                    <span class="block truncate text-sm font-semibold">{{ $destination->name }}</span>
                                    <span class="block text-xs text-zinc-500">
                                        {{ $destination->categoryLabel() }}@if ($destination->elevation) · {{ fa_num($destination->elevation) }} متر @endif
                                    </span>
                                </span>
                            </a>
                        @endforeach
                    </div>
                </x-admin.card>
            @endif
        </div>

        <div class="flex flex-col gap-6">
            @if ($completeness['items'])
                <x-admin.card :title="$d['entity'] ? 'تکمیل پروفایل' : 'تکمیل حساب'">
                    <div class="flex items-center gap-5">
                        <div class="relative size-20 shrink-0">
                            <svg viewBox="0 0 36 36" class="size-20 -rotate-90" dir="ltr">
                                <circle cx="18" cy="18" r="15.9" fill="none" stroke="currentColor" stroke-width="3" class="text-zinc-100 dark:text-zinc-800" />
                                <circle cx="18" cy="18" r="15.9" fill="none" stroke="{{ $ringColor }}" stroke-width="3" stroke-linecap="round" stroke-dasharray="{{ $percent }}, 100" />
                            </svg>
                            <span class="absolute inset-0 flex items-center justify-center text-sm font-extrabold">{{ fa_num($percent) }}٪</span>
                        </div>
                        <p class="text-sm leading-7 text-zinc-600 dark:text-zinc-400">
                            {{ $percent === 100 ? 'عالی! پروفایل شما کامل است.' : 'با تکمیل موارد زیر، اعتبار و دیده‌شدن شما بیشتر می‌شود.' }}
                        </p>
                    </div>
                    <ul class="mt-4 space-y-2">
                        @foreach ($completeness['items'] as $item)
                            <li>
                                <a href="{{ $item['href'] }}" wire:navigate @class(['flex items-center gap-2.5 text-sm', 'text-zinc-400 line-through' => $item['done'], 'text-zinc-700 hover:text-brand dark:text-zinc-300' => ! $item['done']])>
                                    @if ($item['done'])
                                        <flux:icon.check-circle class="size-5 shrink-0 text-emerald-500" />
                                    @else
                                        <span class="size-5 shrink-0 rounded-full border-2 border-zinc-300 dark:border-zinc-600"></span>
                                    @endif
                                    {{ $item['label'] }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </x-admin.card>
            @endif

            <x-admin.card title="دسترسی سریع" :padded="false">
                <ul class="divide-y divide-zinc-100 dark:divide-zinc-800">
                    @foreach ($d['actions'] as $action)
                        <li>
                            <a href="{{ $action['href'] }}" @if ($action['external']) target="_blank" @else wire:navigate @endif class="flex items-center gap-3 px-5 py-3 text-sm transition hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                                <flux:icon :name="$action['icon']" class="size-5 text-zinc-400" />
                                <span class="flex-1">{{ $action['label'] }}</span>
                                <flux:icon.chevron-left class="size-4 text-zinc-300" />
                            </a>
                        </li>
                    @endforeach
                </ul>
            </x-admin.card>

            <x-admin.card title="خلاصه حساب">
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between gap-3"><dt class="text-zinc-500">موبایل</dt><dd dir="ltr" class="tabular-nums">{{ $user->mobile }}</dd></div>
                    <div class="flex justify-between gap-3"><dt class="text-zinc-500">نام کاربری</dt><dd dir="ltr">{{ '@'.$user->username }}</dd></div>
                    @if ($user->primaryClub)
                        <div class="flex justify-between gap-3"><dt class="text-zinc-500">باشگاه اصلی</dt><dd>{{ $user->primaryClub->name }}</dd></div>
                    @endif
                    <div class="flex justify-between gap-3"><dt class="text-zinc-500">عضویت از</dt><dd>{{ fa_date($user->created_at, false, true) }}</dd></div>
                </dl>
            </x-admin.card>
        </div>
    </div>
</x-layouts::app>
