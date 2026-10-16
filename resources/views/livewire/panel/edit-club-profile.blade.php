<div class="mx-auto max-w-2xl space-y-6">
    <div>
        <flux:heading size="xl">ویرایش باشگاه</flux:heading>
        <flux:subheading dir="ltr">/panel/club/profile</flux:subheading>
    </div>

    <flux:card>
        <form wire:submit="save" class="space-y-4">
            <flux:input wire:model="name" label="نام باشگاه" />
            <flux:textarea wire:model="summary" label="خلاصه" rows="3" />
            <flux:textarea wire:model="description" label="توضیحات" rows="6" />
            <div class="grid gap-4 sm:grid-cols-2">
                <flux:input wire:model="city" label="شهر" />
                <flux:input wire:model="address" label="آدرس" />
            </div>
            <flux:button type="submit" variant="primary">ذخیره باشگاه</flux:button>
        </form>
    </flux:card>
</div>
