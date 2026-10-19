<?php

namespace App\Support;

use App\Enums\FundExpenseCategory;
use App\Enums\FundIncomeSource;
use App\Models\FundCampaign;
use App\Models\FundExpense;
use App\Models\FundIncome;
use App\Models\MembershipDue;
use App\Models\User;
use Illuminate\Support\Collection;

class FundReport
{
    /**
     * @return array{
     *     income: int,
     *     inKind: int,
     *     expense: int,
     *     balance: int,
     *     incomeBySource: list<array{source: FundIncomeSource, total: int, percent: int}>,
     *     expenseByCategory: list<array{category: FundExpenseCategory, total: int, percent: int}>
     * }
     */
    public static function summary(): array
    {
        $income = (int) FundIncome::query()->where('is_in_kind', false)->sum('amount');
        $inKind = (int) FundIncome::query()->where('is_in_kind', true)->sum('amount');
        $expense = (int) FundExpense::query()->sum('amount');

        $bySource = FundIncome::query()->where('is_in_kind', false)->selectRaw('source, sum(amount) as total')->groupBy('source')->pluck('total', 'source');
        $byCategory = FundExpense::query()->selectRaw('category, sum(amount) as total')->groupBy('category')->pluck('total', 'category');

        return [
            'income' => $income,
            'inKind' => $inKind,
            'expense' => $expense,
            'balance' => $income - $expense,
            'incomeBySource' => self::breakdown(FundIncomeSource::cases(), $bySource, $income, 'source'),
            'expenseByCategory' => self::breakdown(FundExpenseCategory::cases(), $byCategory, $expense, 'category'),
        ];
    }

    /**
     * @return Collection<int, FundCampaign>
     */
    public static function campaigns(): Collection
    {
        return FundCampaign::query()
            ->withSum('incomes', 'amount')
            ->orderByDesc('is_active')
            ->latest()
            ->get();
    }

    /**
     * @return array{dues: Collection<int, MembershipDue>, total: int, paid: int, remaining: int, percent: int}
     */
    public static function memberDues(User $user): array
    {
        $dues = $user->membershipDues()->withSum('payments', 'amount')->orderByDesc('period')->get();
        $total = (int) $dues->sum('amount');
        $paid = (int) $dues->sum(fn (MembershipDue $due): int => min($due->amount, $due->paidAmount()));

        return [
            'dues' => $dues,
            'total' => $total,
            'paid' => $paid,
            'remaining' => max(0, $total - $paid),
            'percent' => $total > 0 ? min(100, (int) floor($paid / $total * 100)) : 0,
        ];
    }

    /**
     * Members holding a level badge (fully paid plan), highest level first.
     *
     * @return Collection<int, User>
     */
    public static function supporters(int $limit = 12): Collection
    {
        return MembershipDue::query()
            ->whereNotNull('membership_plan_id')
            ->with(['plan', 'user:id,name,username'])
            ->withSum('payments', 'amount')
            ->get()
            ->filter(fn (MembershipDue $due): bool => $due->isActive() && $due->user !== null)
            ->sortByDesc(fn (MembershipDue $due): int => $due->plan->level)
            ->unique('user_id')
            ->take($limit)
            ->values();
    }

    /**
     * @return Collection<int, FundExpense>
     */
    public static function recentExpenses(int $limit = 50): Collection
    {
        return FundExpense::query()->with('campaign:id,title')->orderByDesc('spent_at')->orderByDesc('id')->limit($limit)->get();
    }

    /**
     * @return Collection<int, FundIncome>
     */
    public static function recentIncomes(int $limit = 50): Collection
    {
        return FundIncome::query()->with(['user:id,name', 'campaign:id,title', 'sponsor:id,name', 'project:id,title'])->orderByDesc('received_at')->orderByDesc('id')->limit($limit)->get();
    }

    /**
     * @param  list<FundIncomeSource|FundExpenseCategory>  $cases
     * @param  Collection<string, mixed>  $totals
     * @return list<array<string, mixed>>
     */
    private static function breakdown(array $cases, Collection $totals, int $grand, string $key): array
    {
        return collect($cases)
            ->map(fn ($case): array => [
                $key => $case,
                'total' => $total = (int) ($totals[$case->value] ?? 0),
                'percent' => $grand > 0 ? (int) round($total / $grand * 100) : 0,
            ])
            ->filter(fn (array $row): bool => $row['total'] > 0)
            ->sortByDesc('total')
            ->values()
            ->all();
    }
}
