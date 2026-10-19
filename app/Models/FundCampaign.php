<?php

namespace App\Models;

use Database\Factories\FundCampaignFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['title', 'description', 'goal_amount', 'ends_at', 'is_active'])]
class FundCampaign extends Model
{
    /** @use HasFactory<FundCampaignFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'goal_amount' => 'integer',
            'ends_at' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function incomes(): HasMany
    {
        return $this->hasMany(FundIncome::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(FundExpense::class);
    }

    public function raisedAmount(): int
    {
        return (int) ($this->incomes_sum_amount ?? $this->incomes()->sum('amount'));
    }

    public function progressPercent(): int
    {
        return $this->goal_amount > 0 ? min(100, (int) floor($this->raisedAmount() / $this->goal_amount * 100)) : 0;
    }
}
