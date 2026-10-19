<?php

namespace App\Models;

use App\Enums\FundExpenseCategory;
use Database\Factories\FundExpenseFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['title', 'category', 'amount', 'spent_at', 'fund_campaign_id', 'support_project_id', 'description'])]
class FundExpense extends Model
{
    /** @use HasFactory<FundExpenseFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'category' => FundExpenseCategory::class,
            'amount' => 'integer',
            'spent_at' => 'date',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(SupportProject::class, 'support_project_id');
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(FundCampaign::class, 'fund_campaign_id');
    }
}
