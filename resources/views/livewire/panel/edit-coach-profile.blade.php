<div class="mx-auto max-w-2xl space-y-6">
    <div>
        <flux:heading size="xl">ویرایش پروفایل تخصصی</flux:heading>
        <flux:subheading>اطلاعات مربی یا سرپرست برای نمایش عمومی</flux:subheading>
    </div>

    <flux:card>
        <form wire:submit="save" class="space-y-4">
            <flux:input wire:model="headline" label="عنوان" />
            <flux:input wire:model="specialty" label="تخصص" />
            <flux:textarea wire:model="bio" label="بیوگرافی" rows="6" />
            <flux:input wire:model="experience_years" label="سال‌های تجربه" type="number" min="0" max="60" />
            <flux:button type="submit" variant="primary">ذخیره پروفایل</flux:button>
        </form>
    </flux:card>
</div>
