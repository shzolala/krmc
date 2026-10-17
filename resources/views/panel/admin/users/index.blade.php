<x-layouts::admin title="مدیریت کاربران">
    <x-admin.page-header
        title="کاربران"
        :subtitle="fa_num($totalUsers).' کاربر ثبت‌نام‌شده'"
        icon="users"
    >
        <flux:button :href="route('panel.admin.roles')" icon="key" wire:navigate>نقش‌ها و دسترسی‌ها</flux:button>
        <flux:button variant="primary" icon="user-plus" :href="route('panel.admin.users.create')" wire:navigate>کاربر جدید</flux:button>
    </x-admin.page-header>

    <div class="flex flex-wrap gap-2">
        <a href="{{ route('panel.admin.users.index', array_filter(['q' => $search])) }}" wire:navigate
            @class([
                'rounded-full border px-3.5 py-1.5 text-xs font-medium transition',
                'border-navy-900 bg-navy-900 text-white dark:border-brand dark:bg-brand dark:text-zinc-900' => $activeRole === null,
                'border-zinc-200 bg-white text-zinc-700 hover:border-zinc-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-300' => $activeRole !== null,
            ])>
            همه ({{ fa_num($totalUsers) }})
        </a>
        @foreach ($roles as $role)
            <a href="{{ route('panel.admin.users.index', array_filter(['role' => $role->value, 'q' => $search])) }}" wire:navigate
                @class([
                    'rounded-full border px-3.5 py-1.5 text-xs font-medium transition',
                    'border-navy-900 bg-navy-900 text-white dark:border-brand dark:bg-brand dark:text-zinc-900' => $activeRole === $role,
                    'border-zinc-200 bg-white text-zinc-700 hover:border-zinc-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-300' => $activeRole !== $role,
                ])>
                {{ $role->label() }} ({{ fa_num($counts[$role->value] ?? 0) }})
            </a>
        @endforeach
    </div>

    <x-admin.card :padded="false">
        <form method="GET" action="{{ route('panel.admin.users.index') }}" class="flex flex-wrap items-center gap-3 border-b border-zinc-100 p-4 dark:border-zinc-800">
            @if ($activeRole)
                <input type="hidden" name="role" value="{{ $activeRole->value }}">
            @endif
            <div class="min-w-56 flex-1">
                <flux:input name="q" :value="$search" icon="magnifying-glass" placeholder="جستجوی نام، موبایل، یوزرنیم یا ایمیل…" />
            </div>
            <flux:button type="submit" icon="magnifying-glass">جستجو</flux:button>
            @if ($search !== '')
                <flux:button variant="ghost" :href="route('panel.admin.users.index', array_filter(['role' => $activeRole?->value]))" wire:navigate>پاک‌کردن</flux:button>
            @endif
        </form>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-zinc-50 text-xs text-zinc-500 dark:bg-zinc-800/50">
                    <tr>
                        <th class="px-4 py-3 text-start font-semibold">کاربر</th>
                        <th class="px-4 py-3 text-start font-semibold">موبایل</th>
                        <th class="px-4 py-3 text-start font-semibold">نقش</th>
                        <th class="px-4 py-3 text-start font-semibold">باشگاه اصلی</th>
                        <th class="px-4 py-3 text-start font-semibold">عضویت</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                    @forelse ($users as $user)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/40">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <flux:avatar size="sm" :name="$user->name" :initials="$user->initials()" />
                                    <div class="min-w-0">
                                        <a href="{{ route('panel.admin.users.edit', $user) }}" wire:navigate class="block truncate font-semibold hover:text-brand">{{ $user->name }}</a>
                                        <a href="{{ route('profiles.show', $user) }}" target="_blank" class="text-xs text-zinc-500 hover:underline" dir="ltr">{{ '@'.$user->username }}</a>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 tabular-nums" dir="ltr">{{ $user->mobile }}</td>
                            <td class="px-4 py-3"><flux:badge size="sm" :color="$user->role->badgeColor()">{{ $user->roleLabel() }}</flux:badge></td>
                            <td class="px-4 py-3 text-zinc-600 dark:text-zinc-400">{{ $user->primaryClub?->name ?? '—' }}</td>
                            <td class="whitespace-nowrap px-4 py-3 text-xs text-zinc-500">{{ fa_date($user->created_at) }}</td>
                            <td class="whitespace-nowrap px-4 py-3 text-end">
                                <div class="inline-flex items-center gap-1">
                                    @if (! $user->isAdmin() && ! $user->is(auth()->user()))
                                        <form method="POST" action="{{ route('panel.admin.users.impersonate', $user) }}">
                                            @csrf
                                            <flux:button size="xs" variant="ghost" type="submit" icon="eye" title="ورود به‌جای کاربر" />
                                        </form>
                                    @endif
                                    <flux:button size="xs" variant="ghost" icon="pencil-square" :href="route('panel.admin.users.edit', $user)" wire:navigate title="ویرایش" />
                                    @unless ($user->is(auth()->user()))
                                        <form method="POST" action="{{ route('panel.admin.users.destroy', $user) }}"
                                            onsubmit="return confirm('کاربر «{{ addslashes($user->name) }}» حذف شود؟')">
                                            @csrf
                                            @method('DELETE')
                                            <flux:button size="xs" variant="ghost" type="submit" icon="trash" class="text-red-600!" title="حذف" />
                                        </form>
                                    @endunless
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-16 text-center text-sm text-zinc-500">
                                <flux:icon.users class="mx-auto mb-3 size-10 text-zinc-300" />
                                کاربری پیدا نشد.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($users->hasPages())
            <div class="border-t border-zinc-100 p-4 dark:border-zinc-800">{{ $users->links() }}</div>
        @endif
    </x-admin.card>
</x-layouts::admin>
