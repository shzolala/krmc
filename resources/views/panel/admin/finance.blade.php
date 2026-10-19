<x-layouts::admin title="امور مالی">
    <x-admin.page-header
        title="صندوق و امور مالی"
        subtitle="نمای کلی منابع، هزینه‌ها و جذب سرمایه"
        icon="banknotes"
    >
        <flux:button :href="route('panel.admin.resources.create', 'incomes')" icon="arrow-down-tray" wire:navigate>ثبت دریافتی</flux:button>
        <flux:button variant="primary" :href="route('panel.admin.resources.create', 'expenses')" icon="plus" wire:navigate>ثبت هزینه</flux:button>
    </x-admin.page-header>

    <x-finance.report
        :summary="$summary"
        :campaigns="$campaigns"
        :can-view-ledger="true"
        :expenses="$expenses"
        :incomes="$incomes"
        :supporters="$supporters"
    />
</x-layouts::admin>
