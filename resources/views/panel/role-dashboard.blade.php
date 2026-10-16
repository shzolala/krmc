<x-layouts::app :title="$panel['label'] ?? 'داشبورد'">
    <div class="flex flex-col gap-6">
        <div>
            <flux:heading size="xl">{{ $panel['label'] }}</flux:heading>
            <flux:subheading>
                خوش آمدید {{ $user->name }}
                · {{ $role->label() }}
                · <span dir="ltr" class="font-mono">{{ $panel['path'] }}</span>
            </flux:subheading>
        </div>

        <div class="grid gap-4 lg:grid-cols-2">
            <flux:card class="space-y-4">
                <flux:heading size="lg">دسترسی‌های این پنل</flux:heading>
                <div class="grid gap-2 sm:grid-cols-2">
                    @foreach ($links as $link)
                        <flux:button :href="route($link['route'])" variant="filled" wire:navigate class="justify-start">
                            {{ $link['label'] }}
                        </flux:button>
                    @endforeach
                </div>
            </flux:card>

            <flux:card class="space-y-3">
                <flux:heading size="lg">خلاصه حساب</flux:heading>
                <div class="space-y-2 text-sm">
                    <div>
                        <flux:text class="text-zinc-500">موبایل</flux:text>
                        <div dir="ltr">{{ $user->mobile }}</div>
                    </div>
                    <div>
                        <flux:text class="text-zinc-500">یوزرنیم</flux:text>
                        <div dir="ltr">
                            <a href="{{ route('profiles.show', $user) }}" class="hover:underline" wire:navigate>
                                {{ '@'.$user->username }}
                            </a>
                        </div>
                    </div>
                    @if ($user->primaryClub)
                        <div>
                            <flux:text class="text-zinc-500">باشگاه اصلی</flux:text>
                            <div>{{ $user->primaryClub->name }}</div>
                        </div>
                    @endif
                    @if ($user->coachProfile)
                        <div>
                            <flux:text class="text-zinc-500">تخصص</flux:text>
                            <div>{{ $user->coachProfile->specialty }}</div>
                        </div>
                    @endif
                    @if ($user->shop)
                        <div>
                            <flux:text class="text-zinc-500">فروشگاه</flux:text>
                            <div>{{ $user->shop->name }}</div>
                        </div>
                    @endif
                </div>
            </flux:card>
        </div>
    </div>
</x-layouts::app>
