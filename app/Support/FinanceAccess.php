<?php

namespace App\Support;

use App\Models\FinanceLedgerAccess;
use App\Models\User;

class FinanceAccess
{
    /**
     * The admin always sees the ledger; everyone else needs a grant for one of their roles or a personal grant.
     */
    public static function canViewLedger(User $user): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return FinanceLedgerAccess::query()
            ->where(fn ($query) => $query
                ->whereIn('role', $user->mainRoles()->map->value->all())
                ->orWhere('user_id', $user->id))
            ->exists();
    }
}
