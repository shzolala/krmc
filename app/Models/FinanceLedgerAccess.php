<?php

namespace App\Models;

use App\Enums\UserRole;
use Database\Factories\FinanceLedgerAccessFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['role', 'user_id', 'note'])]
class FinanceLedgerAccess extends Model
{
    /** @use HasFactory<FinanceLedgerAccessFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return ['role' => UserRole::class];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected function granteeLabel(): Attribute
    {
        return Attribute::get(fn (): string => $this->user_id !== null
            ? 'کاربر: '.($this->user?->name ?? '—')
            : 'گروه: '.($this->role?->label() ?? '—'));
    }
}
