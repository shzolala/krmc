<x-layouts::app title="داشبورد مدیر">
    <div class="flex flex-col gap-6">
        <div>
            <flux:heading size="xl">پنل مدیر</flux:heading>
            <flux:subheading>مسیر اختصاصی: <span dir="ltr" class="font-mono">/panel/admin</span></flux:subheading>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <flux:card class="space-y-1">
                <flux:text class="text-sm">کاربران</flux:text>
                <flux:heading size="xl">{{ number_format($stats['users']) }}</flux:heading>
            </flux:card>
            <flux:card class="space-y-1">
                <flux:text class="text-sm">باشگاه‌ها</flux:text>
                <flux:heading size="xl">{{ number_format($stats['clubs']) }}</flux:heading>
            </flux:card>
            <flux:card class="space-y-1">
                <flux:text class="text-sm">فروشگاه‌ها</flux:text>
                <flux:heading size="xl">{{ number_format($stats['shops']) }}</flux:heading>
            </flux:card>
            <flux:card class="space-y-1">
                <flux:text class="text-sm">نظرات در انتظار</flux:text>
                <flux:heading size="xl">{{ number_format($stats['pending_reviews']) }}</flux:heading>
            </flux:card>
        </div>

        <div class="grid gap-3 sm:grid-cols-3">
            <flux:button :href="route('panel.admin.users.index')" variant="primary" wire:navigate>
                مدیریت کاربران
            </flux:button>
            <flux:button :href="route('panel.admin.reviews')" variant="filled" wire:navigate>
                مدیریت نظرات
            </flux:button>
            <flux:button :href="route('profile.edit')" variant="ghost" wire:navigate>
                تنظیمات حساب
            </flux:button>
        </div>
    </div>
</x-layouts::app>
