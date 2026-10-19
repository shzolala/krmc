<?php

namespace App\Models;

use App\Enums\FundIncomeSource;
use Database\Factories\FundIncomeFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['source', 'amount', 'received_at', 'user_id', 'membership_due_id', 'fund_campaign_id', 'sponsor_id', 'support_project_id', 'is_in_kind', 'in_kind_description', 'installment_no', 'payer_name', 'note'])]
class FundIncome extends Model
{
    /** @use HasFactory<FundIncomeFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'source' => FundIncomeSource::class,
            'amount' => 'integer',
            'is_in_kind' => 'boolean',
            'installment_no' => 'integer',
            'received_at' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function membershipDue(): BelongsTo
    {
        return $this->belongsTo(MembershipDue::class);
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(FundCampaign::class, 'fund_campaign_id');
    }

    public function sponsor(): BelongsTo
    {
        return $this->belongsTo(Sponsor::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(SupportProject::class, 'support_project_id');
    }

    public function payerLabel(): string
    {
        return $this->user?->name ?? $this->sponsor?->name ?? $this->payer_name ?? 'ناشناس';
    }
}
