@php
    $greeting = match (true) {
        now()->hour < 12 => 'صبح بخیر',
        now()->hour < 18 => 'روز بخیر',
        default => 'شب بخیر',
    };
@endphp

<x-layouts::app title="پنل من">
    <section class="relative overflow-hidden rounded-2xl bg-gradient-to-l from-navy-950 via-navy-900 to-navy-700 p-6 text-white shadow-sm sm:p-8">
        <div class="pointer-events-none absolute -start-16 -top-16 size-64 rounded-full bg-brand/15 blur-3xl"></div>
        <div class="relative flex flex-wrap items-center justify-between gap-6">
            <div class="flex items-center gap-4">
                <span class="flex size-16 shrink-0 items-center justify-center rounded-2xl bg-white/10 text-2xl font-extrabold text-brand-soft ring-1 ring-white/20">
                    {{ $user->initials() }}
                </span>
                <div class="min-w-0">
                    <p class="text-sm text-white/70">{{ $greeting }}،</p>
                    <h1 class="truncate text-2xl font-extrabold sm:text-3xl">{{ $user->name }}</h1>
                    <div class="mt-2 flex flex-wrap items-center gap-2 text-xs">
                        @foreach ($user->mainRoles() as $role)
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-white/10 px-3 py-1 ring-1 ring-white/15">
                                <flux:icon :name="$role->icon()" class="size-3.5" /> {{ $role->label() }}
                            </span>
                        @endforeach
                        <x-membership-badge :user="$user" on-dark class="!px-3 !py-1 !text-xs" />
                    </div>
                </div>
            </div>
            <p class="max-w-sm text-sm leading-7 text-white/75">
                با یک حساب و یک شماره موبایل، همه‌ی بخش‌هایی که مدیریت می‌کنید یک‌جا کنار هم هستند.
            </p>
        </div>
    </section>

    <div class="grid gap-6 lg:grid-cols-2">
        @foreach ($sections as $section)
            @php
                $data = $section['dashboard_data'];
                $percent = $data['completeness']['percent'];
                $isPersonal = $section['role'] === \App\Enums\UserRole::Climber;
            @endphp
            <x-admin.card :padded="false">
                <div class="flex items-start gap-4 p-5">
                    <span class="flex size-12 shrink-0 items-center justify-center rounded-xl bg-navy-900 text-brand">
                        <flux:icon :name="$section['icon']" class="size-6" />
                    </span>
                    <div class="min-w-0 flex-1">
                        <h2 class="truncate text-lg font-bold">{{ $section['title'] }}</h2>
                        <div class="mt-1 flex flex-wrap items-center gap-1.5">
                            <flux:badge size="sm" :color="$section['role']->badgeColor()">{{ $section['subtitle'] }}</flux:badge>
                            @foreach ($data['badges'] as $badge)
                                <flux:badge size="sm" :color="$badge['color']">{{ $badge['label'] }}</flux:badge>
                            @endforeach
                        </div>
                    </div>
                </div>

                @if ($data['stats'])
                    <dl class="grid grid-cols-3 divide-x divide-zinc-100 border-y border-zinc-100 text-center rtl:divide-x-reverse dark:divide-zinc-800 dark:border-zinc-800">
                        @foreach (array_slice($data['stats'], 0, 3) as $stat)
                            <div class="px-2 py-3">
                                <dd class="truncate text-lg font-extrabold">{{ $stat['value'] }}</dd>
                                <dt class="truncate text-xs text-zinc-500">{{ $stat['label'] }}</dt>
                            </div>
                        @endforeach
                    </dl>
                @endif

                <div class="flex flex-col gap-4 p-5">
                    @if ($data['completeness']['items'])
                        <div>
                            <div class="mb-1.5 flex items-center justify-between text-xs">
                                <span class="text-zinc-500">{{ $isPersonal ? 'تکمیل حساب' : 'تکمیل پروفایل' }}</span>
                                <span class="font-bold">{{ fa_num($percent) }}٪</span>
                            </div>
                            <div class="h-2 overflow-hidden rounded-full bg-zinc-100 dark:bg-zinc-800">
                                <div class="h-full rounded-full {{ $percent >= 80 ? 'bg-emerald-500' : ($percent >= 50 ? 'bg-amber-500' : 'bg-red-500') }}" style="width: {{ $percent }}%"></div>
                            </div>
                        </div>
                    @endif

                    <div class="flex flex-wrap gap-2">
                        <flux:button variant="primary" size="sm" icon="squares-2x2" :href="$section['dashboard']" wire:navigate>باز کردن بخش</flux:button>
                        @if ($section['edit'])
                            <flux:button size="sm" icon="pencil-square" :href="$section['edit']" wire:navigate>ویرایش</flux:button>
                        @endif
                        @if ($section['public'])
                            <flux:button size="sm" variant="ghost" icon="arrow-top-right-on-square" :href="$section['public']" target="_blank">صفحه عمومی</flux:button>
                        @endif
                    </div>
                </div>
            </x-admin.card>
        @endforeach
    </div>

    <x-admin.card title="دسترسی سریع" :padded="false">
        <div class="grid divide-zinc-100 sm:grid-cols-3 sm:divide-x sm:rtl:divide-x-reverse dark:divide-zinc-800">
            @foreach ([
                ['صندوق و امور مالی', 'banknotes', route('panel.finance')],
                ['پلن‌های عضویت', 'sparkles', route('panel.membership')],
                ['حمایت از کوهستان', 'building-library', route('panel.sponsorship')],
            ] as [$label, $icon, $href])
                <a href="{{ $href }}" wire:navigate class="flex items-center gap-3 px-5 py-4 text-sm transition hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                    <flux:icon :name="$icon" class="size-5 text-zinc-400" />
                    <span class="flex-1">{{ $label }}</span>
                    <flux:icon.chevron-left class="size-4 text-zinc-300" />
                </a>
            @endforeach
        </div>
    </x-admin.card>
</x-layouts::app>
