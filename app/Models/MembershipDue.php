<?php

namespace App\Models;

use Database\Factories\MembershipDueFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['user_id', 'membership_plan_id', 'title', 'period', 'amount', 'term_months', 'discount_percent', 'starts_on', 'ends_on', 'due_date', 'note'])]
class MembershipDue extends Model
{
    /** @use HasFactory<MembershipDueFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'amount' => 'integer',
            'term_months' => 'integer',
            'discount_percent' => 'integer',
            'starts_on' => 'date',
            'ends_on' => 'date',
            'due_date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(MembershipPlan::class, 'membership_plan_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(FundIncome::class);
    }

    public function paidAmount(): int
    {
        return (int) ($this->payments_sum_amount ?? $this->payments()->sum('amount'));
    }

    public function remainingAmount(): int
    {
        return max(0, $this->amount - $this->paidAmount());
    }

    public function paidPercent(): int
    {
        return $this->amount > 0 ? min(100, (int) floor($this->paidAmount() / $this->amount * 100)) : 100;
    }

    public function isSettled(): bool
    {
        return $this->amount > 0 && $this->remainingAmount() === 0;
    }

    /**
     * Fully paid and not yet expired.
     */
    public function isActive(): bool
    {
        return $this->isSettled() && ($this->ends_on === null || ! $this->ends_on->isPast());
    }

    public function isExpired(): bool
    {
        return $this->ends_on !== null && $this->ends_on->isPast();
    }

    public function remainingDays(): int
    {
        return $this->ends_on === null ? 0 : max(0, (int) now()->startOfDay()->diffInDays($this->ends_on->startOfDay(), false));
    }

    public function isOverdue(): bool
    {
        return $this->remainingAmount() > 0 && $this->due_date !== null && $this->due_date->isPast();
    }
}
