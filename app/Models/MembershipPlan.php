<?php

namespace App\Models;

use App\Enums\PlanBadgeColor;
use App\Enums\UserRole;
use Database\Factories\MembershipPlanFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'role', 'level', 'price', 'duration_days', 'allow_monthly', 'allow_semiannual', 'allow_annual', 'discount_semiannual', 'discount_annual', 'description', 'features', 'badge_label', 'badge_color', 'badge_icon', 'is_active'])]
class MembershipPlan extends Model
{
    /** @use HasFactory<MembershipPlanFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    public const MAX_LEVEL = 3;

    /**
     * Supported purchase terms: months => days.
     *
     * @var array<int, int>
     */
    public const TERM_DAYS = [1 => 30, 6 => 180, 12 => 365];

    /**
     * @var array<int, string>
     */
    public const TERM_LABELS = [1 => 'یک‌ماهه', 6 => 'شش‌ماهه', 12 => 'یک‌ساله'];

    /**
     * @var array<string, string>
     */
    public const TERM_LABELS_STRING = ['1' => 'یک‌ماهه', '6' => 'شش‌ماهه', '12' => 'یک‌ساله'];

    public const BADGE_ICONS = ['star', 'shield-check', 'trophy', 'sparkles', 'fire', 'heart', 'flag', 'bolt'];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'role' => UserRole::class,
            'level' => 'integer',
            'price' => 'integer',
            'duration_days' => 'integer',
            'allow_monthly' => 'boolean',
            'allow_semiannual' => 'boolean',
            'allow_annual' => 'boolean',
            'discount_semiannual' => 'integer',
            'discount_annual' => 'integer',
            'features' => 'array',
            'badge_color' => PlanBadgeColor::class,
            'is_active' => 'boolean',
        ];
    }

    public function dues(): HasMany
    {
        return $this->hasMany(MembershipDue::class);
    }

    #[Scope]
    protected function active(Builder $query): void
    {
        $query->where('is_active', true);
    }

    #[Scope]
    protected function availableFor(Builder $query, User $user): void
    {
        $query->where(fn (Builder $inner) => $inner->whereNull('role')->orWhereIn('role', $user->mainRoles()->map->value->all()));
    }

    public function badgeText(): string
    {
        return $this->badge_label ?: $this->name;
    }

    protected function audience(): Attribute
    {
        return Attribute::get(fn (): string => $this->role?->label() ?? 'همه گروه‌ها');
    }

    /**
     * Term lengths (in months) members may pick for this plan; defaults to annual.
     *
     * @return list<int>
     */
    public function offeredTerms(): array
    {
        $terms = array_keys(array_filter([
            1 => $this->allow_monthly,
            6 => $this->allow_semiannual,
            12 => $this->allow_annual,
        ]));

        return $terms === [] ? [12] : array_values($terms);
    }

    public function offersTerm(int $months): bool
    {
        return in_array($months, $this->offeredTerms(), true);
    }

    public function discountFor(int $months): int
    {
        return match ($months) {
            6 => $this->discount_semiannual,
            12 => $this->discount_annual,
            default => 0,
        };
    }

    /**
     * Price breakdown of one term. `price` is the list price for `duration_days` (365 by default).
     *
     * @return array{months: int, days: int, label: string, list: int, discount: int, final: int, per_month: int}
     */
    public function termPrice(int $months): array
    {
        $days = self::TERM_DAYS[$months] ?? 365;
        $list = self::roundToThousand($this->price * $days / max(1, $this->duration_days));
        $discount = $this->discountFor($months);
        $final = self::roundToThousand($list * (100 - $discount) / 100);

        return [
            'months' => $months,
            'days' => $days,
            'label' => self::TERM_LABELS[$months] ?? $months.' ماهه',
            'list' => $list,
            'discount' => $discount,
            'final' => $final,
            'per_month' => self::roundToThousand($final / $months),
        ];
    }

    /**
     * @return list<array{months: int, days: int, label: string, list: int, discount: int, final: int, per_month: int}>
     */
    public function terms(): array
    {
        return array_map(fn (int $months): array => $this->termPrice($months), $this->offeredTerms());
    }

    private static function roundToThousand(float|int $value): int
    {
        return (int) (round($value / 1000) * 1000);
    }
}
