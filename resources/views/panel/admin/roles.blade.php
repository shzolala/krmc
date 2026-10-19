<x-layouts::admin title="نقش‌ها و دسترسی‌ها">
    <x-admin.page-header
        title="نقش‌ها و سطوح دسترسی"
        subtitle="هر نقش به چه بخش‌هایی دسترسی دارد. یک حساب می‌تواند چند نقش را هم‌زمان داشته باشد (با داشتن باشگاه، فروشگاه یا پروفایل مربی)، پس مجموع شمارش‌ها از تعداد کاربران بیشتر است."
        icon="key"
        :back="route('panel.admin.dashboard')"
        back-label="داشبورد"
    />

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
        @foreach ($roles as $item)
            @php $role = $item['role']; @endphp
            <x-admin.card class="flex flex-col">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <span class="flex size-11 items-center justify-center rounded-xl bg-zinc-100 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-200">
                            <flux:icon :name="$role->icon()" class="size-6" />
                        </span>
                        <div>
                            <h2 class="font-bold">{{ $role->label() }}</h2>
                            <span class="text-xs text-zinc-500" dir="ltr">{{ $role->value }}</span>
                        </div>
                    </div>
                    <flux:badge :color="$role->badgeColor()">{{ fa_num($item['count']) }} کاربر</flux:badge>
                </div>

                <p class="mt-3 text-sm text-zinc-600 dark:text-zinc-400">{{ $role->description() }}</p>

                <dl class="mt-4 space-y-1.5 text-xs">
                    <div class="flex justify-between gap-2">
                        <dt class="text-zinc-500">مسیر پنل</dt>
                        <dd dir="ltr" class="font-mono">{{ $item['panel']['path'] ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between gap-2">
                        <dt class="text-zinc-500">پروفایل عمومی</dt>
                        <dd>{{ $role->hasPublicDirectoryProfile() ? 'دارد' : 'ندارد' }}</dd>
                    </div>
                </dl>

                <div class="mt-5 flex flex-wrap gap-2 border-t border-zinc-100 pt-4 dark:border-zinc-800">
                    <flux:button size="sm" :href="route('panel.admin.users.index', ['role' => $role->value])" icon="users" wire:navigate>کاربران</flux:button>
                    <flux:button size="sm" variant="ghost" :href="$item['addUrl']" icon="user-plus" wire:navigate>{{ $item['addLabel'] }}</flux:button>
                    @if ($item['sample'] && $role !== \App\Enums\UserRole::Admin)
                        <form method="POST" action="{{ route('panel.admin.users.impersonate', $item['sample']) }}" class="ms-auto">
                            @csrf
                            <flux:button size="sm" variant="ghost" type="submit" icon="eye" title="مشاهده پنل این نقش با حساب {{ $item['sample']->name }}">
                                مشاهده پنل
                            </flux:button>
                        </form>
                    @endif
                </div>
            </x-admin.card>
        @endforeach
    </div>

    <x-admin.card title="ماتریس دسترسی‌ها" description="مقایسه توانایی‌های هر نقش در سایت" :padded="false">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-zinc-50 text-xs text-zinc-500 dark:bg-zinc-800/50">
                    <tr>
                        <th class="px-5 py-3 text-start font-semibold">قابلیت</th>
                        @foreach ($roles as $item)
                            <th class="px-3 py-3 text-center font-semibold">{{ $item['role']->label() }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                    @foreach ($matrix as $row)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/40">
                            <td class="px-5 py-3">{{ $row['label'] }}</td>
                            @foreach ($roles as $item)
                                <td class="px-3 py-3 text-center">
                                    @if (in_array($item['role'], $row['roles'], true))
                                        <flux:icon.check-circle class="mx-auto size-5 text-emerald-600" />
                                    @else
                                        <span class="text-zinc-300 dark:text-zinc-600">—</span>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-admin.card>
</x-layouts::admin>
