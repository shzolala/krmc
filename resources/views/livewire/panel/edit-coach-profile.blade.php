<div class="flex flex-col gap-6">
    <x-admin.page-header title="ویرایش پروفایل تخصصی" subtitle="اطلاعات مربی یا سرپرست برای نمایش عمومی" icon="academic-cap" :back="route(($profile->is_leader ? 'panel.leader.dashboard' : 'panel.coach.dashboard'))" back-label="داشبورد" />

    <form wire:submit="save" class="grid gap-6 lg:grid-cols-3">
        <div class="flex flex-col gap-6 lg:col-span-2">
            <x-admin.card title="معرفی">
                <div class="space-y-5">
                    <flux:input wire:model="headline" label="عنوان / سمت" />
                    <flux:input wire:model="specialty" label="تخصص" />
                    <flux:textarea wire:model="bio" label="بیوگرافی" rows="8" />
                </div>
            </x-admin.card>
            <x-admin.card title="سوابق">
                <flux:input wire:model="experience_years" label="سال‌های تجربه" type="number" min="0" max="60" dir="ltr" />
            </x-admin.card>
            <div class="flex justify-end">
                <flux:button type="submit" variant="primary" icon="check">ذخیره پروفایل</flux:button>
            </div>
        </div>
        <div class="flex flex-col gap-6">
            <x-admin.card title="وضعیت در سایت">
                <div class="flex flex-wrap gap-2">
                    @if ($profile?->is_verified) <flux:badge color="green">تأیید شده</flux:badge> @else <flux:badge color="amber">در انتظار تأیید</flux:badge> @endif
                    @if ($profile?->is_featured) <flux:badge color="blue">ویژه</flux:badge> @endif
                    @if ($profile?->is_premium) <flux:badge color="purple">پریمیوم</flux:badge> @endif
                </div>
                <p class="mt-3 text-xs leading-6 text-zinc-500">تأیید هویت، نمایش ویژه و عضویت پریمیوم توسط مدیریت سایت تعیین می‌شود.</p>
                @if ($profile)
                    <flux:button class="mt-4 w-full" icon="arrow-top-right-on-square" href="{{ route('coaches.show', $profile) }}" target="_blank">مشاهده صفحه عمومی</flux:button>
                @endif
            </x-admin.card>
            <x-admin.card title="نکته">
                <p class="text-sm leading-7 text-zinc-600 dark:text-zinc-400">توضیحات کامل و دقیق باعث می‌شود کوهنوردان راحت‌تر شما را پیدا کنند و به شما اعتماد کنند.</p>
            </x-admin.card>
        </div>
    </form>
</div>
