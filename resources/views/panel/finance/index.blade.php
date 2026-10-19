<x-layouts::app title="امور مالی">
    <x-admin.page-header
        title="صندوق و امور مالی"
        subtitle="سهم شما و وضعیت کل منابع مالی انجمن، شفاف و قابل پیگیری"
        icon="banknotes"
    />

    <x-finance.report
        :summary="$summary"
        :campaigns="$campaigns"
        :member-dues="$memberDues"
        :can-view-ledger="$canViewLedger"
        :expenses="$expenses"
        :incomes="$incomes"
        :supporters="$supporters"
    />
</x-layouts::app>
