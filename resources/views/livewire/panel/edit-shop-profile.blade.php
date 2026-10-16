<div class="mx-auto max-w-2xl space-y-6">
    <div>
        <flux:heading size="xl">ویرایش فروشگاه</flux:heading>
        <flux:subheading dir="ltr">/panel/shop/profile</flux:subheading>
    </div>

    <flux:card>
        <form wire:submit="save" class="space-y-4">
            <flux:input wire:model="name" label="نام فروشگاه" />
            <flux:textarea wire:model="summary" label="خلاصه" rows="3" />
            <div class="grid gap-4 sm:grid-cols-2">
                <flux:input wire:model="city" label="شهر" />
                <flux:input wire:model="address" label="آدرس" />
            </div>
            <flux:button type="submit" variant="primary">ذخیره فروشگاه</flux:button>
        </form>
    </flux:card>
</div>
