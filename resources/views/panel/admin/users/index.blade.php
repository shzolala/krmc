<x-layouts::app title="مدیریت کاربران">
    <div class="flex flex-col gap-6">
        <div class="flex flex-wrap items-end justify-between gap-3">
            <div>
                <flux:heading size="xl">کاربران</flux:heading>
                <flux:subheading dir="ltr">/panel/admin/users</flux:subheading>
            </div>
            <flux:button :href="route('panel.admin.dashboard')" variant="ghost" wire:navigate>بازگشت</flux:button>
        </div>

        <flux:card class="overflow-x-auto p-0">
            <table class="min-w-full divide-y divide-zinc-200 text-sm dark:divide-zinc-700">
                <thead class="bg-zinc-50 text-right dark:bg-zinc-900">
                    <tr>
                        <th class="px-4 py-3 font-semibold">نام</th>
                        <th class="px-4 py-3 font-semibold">موبایل</th>
                        <th class="px-4 py-3 font-semibold">یوزرنیم</th>
                        <th class="px-4 py-3 font-semibold">نقش</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                    @foreach ($users as $user)
                        <tr>
                            <td class="px-4 py-3 font-medium">{{ $user->name }}</td>
                            <td class="px-4 py-3" dir="ltr">{{ $user->mobile }}</td>
                            <td class="px-4 py-3" dir="ltr">
                                <a href="{{ route('profiles.show', $user) }}" class="hover:underline" wire:navigate>
                                    {{ '@'.$user->username }}
                                </a>
                            </td>
                            <td class="px-4 py-3">
                                <flux:badge size="sm">{{ $user->roleLabel() }}</flux:badge>
                            </td>
                            <td class="px-4 py-3 text-left">
                                <flux:button size="sm" variant="ghost" :href="route('panel.admin.users.edit', $user)" wire:navigate>
                                    ویرایش
                                </flux:button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </flux:card>

        {{ $users->links() }}
    </div>
</x-layouts::app>
