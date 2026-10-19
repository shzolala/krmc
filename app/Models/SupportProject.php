<?php

namespace App\Models;

use App\Enums\ProjectCategory;
use App\Enums\ProjectStatus;
use Database\Factories\SupportProjectFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

#[Fillable([
    'title',
    'slug',
    'category',
    'summary',
    'description',
    'budget',
    'status',
    'cover_path',
    'starts_on',
    'ends_on',
    'is_featured',
    'sort_order',
])]
class SupportProject extends Model
{
    /** @use HasFactory<SupportProjectFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'category' => ProjectCategory::class,
            'status' => ProjectStatus::class,
            'budget' => 'integer',
            'starts_on' => 'date',
            'ends_on' => 'date',
            'is_featured' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function activities(): HasMany
    {
        return $this->hasMany(ProjectActivity::class)->orderByDesc('happened_on')->orderByDesc('id');
    }

    public function incomes(): HasMany
    {
        return $this->hasMany(FundIncome::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(FundExpense::class);
    }

    /**
     * Adds cash_raised, in_kind_raised and spent_total aggregates.
     */
    #[Scope]
    protected function withFunding(Builder $query): void
    {
        $query
            ->withSum(['incomes as cash_raised' => fn (Builder $incomes) => $incomes->where('is_in_kind', false)], 'amount')
            ->withSum(['incomes as in_kind_raised' => fn (Builder $incomes) => $incomes->where('is_in_kind', true)], 'amount')
            ->withSum('expenses as spent_total', 'amount');
    }

    public function cashRaised(): int
    {
        return (int) ($this->cash_raised ?? $this->incomes()->where('is_in_kind', false)->sum('amount'));
    }

    public function inKindRaised(): int
    {
        return (int) ($this->in_kind_raised ?? $this->incomes()->where('is_in_kind', true)->sum('amount'));
    }

    public function spentTotal(): int
    {
        return (int) ($this->spent_total ?? $this->expenses()->sum('amount'));
    }

    /**
     * Cash plus the monetary equivalent of in-kind (bartered) support.
     */
    public function raisedTotal(): int
    {
        return $this->cashRaised() + $this->inKindRaised();
    }

    public function progressPercent(): int
    {
        return $this->budget > 0 ? min(100, (int) floor($this->raisedTotal() / $this->budget * 100)) : 0;
    }

    /**
     * Sponsors of this project with each of their contributions, biggest supporter first.
     *
     * @return Collection<int, array{sponsor: Sponsor, total: int, contributions: Collection<int, FundIncome>}>
     */
    public function supporters(): Collection
    {
        return $this->incomes()
            ->whereNotNull('sponsor_id')
            ->with('sponsor')
            ->orderBy('installment_no')
            ->orderBy('received_at')
            ->get()
            ->filter(fn (FundIncome $income): bool => $income->sponsor?->status->value === 'active')
            ->groupBy('sponsor_id')
            ->map(fn (Collection $contributions): array => [
                'sponsor' => $contributions->first()->sponsor,
                'total' => (int) $contributions->sum('amount'),
                'contributions' => $contributions->values(),
            ])
            ->sortByDesc('total')
            ->values();
    }
}
