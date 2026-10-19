<?php

namespace App\Support\Admin\Resources;

use App\Enums\UserRole;
use App\Models\FinanceLedgerAccess;
use App\Models\User;
use App\Support\Admin\AdminResource;
use App\Support\Admin\Column;
use App\Support\Admin\Field;
use Illuminate\Database\Eloquent\Model;

class LedgerAccessResource extends AdminResource
{
    public function model(): string
    {
        return FinanceLedgerAccess::class;
    }

    public function key(): string
    {
        return 'ledger-access';
    }

    public function singular(): string
    {
        return 'دسترسی به ریز مالی';
    }

    public function plural(): string
    {
        return 'دسترسی به ریز مالی';
    }

    public function icon(): string
    {
        return 'eye';
    }

    public function group(): string
    {
        return 'finance';
    }

    public function titleAttribute(): string
    {
        return 'note';
    }

    public function recordTitle(Model $record): string
    {
        return $record->grantee_label;
    }

    public function searchable(): array
    {
        return ['note'];
    }

    public function with(): array
    {
        return ['user:id,name'];
    }

    public function columns(): array
    {
        return [
            Column::title('grantee_label', 'گروه یا کاربر', false),
            Column::text('note', 'یادداشت'),
        ];
    }

    public function fields(): array
    {
        $roles = collect(UserRole::cases())
            ->reject(fn (UserRole $role): bool => $role === UserRole::Admin)
            ->mapWithKeys(fn (UserRole $role): array => [$role->value => $role->label()])
            ->all();

        return [
            'دسترسی' => [
                Field::select('role', 'گروه (همه اعضای این نقش)', $roles)->rules(['required_without:user_id']),
                Field::relation('user_id', 'یا یک کاربر مشخص', User::class)->rules(['required_without:role']),
                Field::text('note', 'یادداشت')->wide(),
            ],
        ];
    }
}
