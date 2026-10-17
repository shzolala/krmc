<div class="flex flex-col gap-6">
    <x-admin.page-header title="ویرایش فروشگاه" subtitle="اطلاعاتی که در دایرکتوری فروشگاه‌ها نمایش داده می‌شود" icon="shopping-bag" :back="route('panel.shop.dashboard')" back-label="داشبورد" />

    <form wire:submit="save" class="grid gap-6 lg:grid-cols-3">
        <div class="flex flex-col gap-6 lg:col-span-2">
            <x-admin.card title="اطلاعات فروشگاه">
                <div class="space-y-5">
                    <flux:input wire:model="name" label="نام فروشگاه" />
                    <flux:textarea wire:model="summary" label="معرفی کوتاه" rows="4" />
                </div>
            </x-admin.card>
            <x-admin.card title="موقعیت">
                <div class="grid gap-5 sm:grid-cols-2">
                    <flux:input wire:model="city" label="شهر" />
                    <flux:input wire:model="address" label="نشانی" />
                </div>
            </x-admin.card>
            <div class="flex justify-end">
                <flux:button type="submit" variant="primary" icon="check">ذخیره فروشگاه</flux:button>
            </div>
        </div>
        <div class="flex flex-col gap-6">
            <x-admin.card title="وضعیت در سایت">
                <div class="flex flex-wrap gap-2">
                    @if ($shop?->is_verified) <flux:badge color="green">تأیید شده</flux:badge> @else <flux:badge color="amber">در انتظار تأیید</flux:badge> @endif
                    @if ($shop?->is_featured) <flux:badge color="blue">ویژه</flux:badge> @endif
                    @if ($shop?->is_premium) <flux:badge color="purple">پریمیوم</flux:badge> @endif
                </div>
                <p class="mt-3 text-xs leading-6 text-zinc-500">تأیید هویت، نمایش ویژه و عضویت پریمیوم توسط مدیریت سایت تعیین می‌شود.</p>
                @if ($shop)
                    <flux:button class="mt-4 w-full" icon="arrow-top-right-on-square" href="{{ route('shops.show', $shop) }}" target="_blank">مشاهده صفحه عمومی</flux:button>
                @endif
            </x-admin.card>
            <x-admin.card title="نکته">
                <p class="text-sm leading-7 text-zinc-600 dark:text-zinc-400">توضیحات کامل و دقیق باعث می‌شود کوهنوردان راحت‌تر شما را پیدا کنند و به شما اعتماد کنند.</p>
            </x-admin.card>
        </div>
    </form>
</div>
