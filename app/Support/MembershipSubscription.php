<?php

namespace App\Support;

use App\Models\MembershipDue;
use App\Models\MembershipPlan;
use App\Models\User;
use Illuminate\Support\Collection;

/**
 * Plan-change rules for one member: levels only go up, paid memberships are upgraded
 * (with a pro-rata credit) rather than replaced, and unpaid choices can be changed freely.
 */
class MembershipSubscription
{
    /**
     * @var Collection<int, MembershipDue>
     */
    private Collection $dues;

    public function __construct(private readonly User $user)
    {
        $this->dues = $user->membershipDues()->with('plan')->withSum('payments', 'amount')->get();
    }

    /**
     * The highest level the member has ever paid for; plans below it can no longer be chosen.
     */
    public function floorLevel(): int
    {
        return (int) $this->dues
            ->filter(fn (MembershipDue $due): bool => $due->plan !== null && $due->paidAmount() > 0)
            ->max(fn (MembershipDue $due): int => $due->plan->level);
    }

    public function activeDue(): ?MembershipDue
    {
        return $this->dues
            ->filter(fn (MembershipDue $due): bool => $due->plan !== null && $due->isActive())
            ->sortByDesc(fn (MembershipDue $due): int => $due->plan->level)
            ->first();
    }

    /**
     * The open (not fully paid, not expired) choice that a new selection modifies.
     */
    public function openDue(): ?MembershipDue
    {
        return $this->dues
            ->filter(fn (MembershipDue $due): bool => ! $due->isSettled() && ! $due->isExpired())
            ->sortByDesc('id')
            ->first();
    }

    /**
     * Unused value of the active membership, deducted when upgrading.
     */
    public function upgradeCredit(): int
    {
        $active = $this->activeDue();

        if ($active === null || $active->starts_on === null || $active->ends_on === null) {
            return 0;
        }

        $total = max(1, (int) $active->starts_on->diffInDays($active->ends_on));

        return (int) floor($active->amount * min(1, $active->remainingDays() / $total) / 1000) * 1000;
    }

    /**
     * @return array{state: 'available'|'selected'|'current'|'upgrade'|'blocked', message: ?string}
     */
    public function evaluate(MembershipPlan $plan): array
    {
        if ($plan->level < $this->floorLevel()) {
            return $this->result('blocked', 'بازگشت به سطح پایین‌تر از سطح قبلی شما ممکن نیست.');
        }

        $active = $this->activeDue();

        if ($active !== null) {
            if ($active->membership_plan_id === $plan->id) {
                return $this->result('current', 'پلن فعلی شما تا '.fa_date($active->ends_on).' معتبر است.');
            }

            return $plan->level > $active->plan->level
                ? $this->result('upgrade', null)
                : $this->result('blocked', 'تا پایان اعتبار پلن فعلی، فقط ارتقا به سطح بالاتر ممکن است.');
        }

        $open = $this->openDue();

        if ($open === null) {
            return $this->result('available', null);
        }

        if ($open->paidAmount() === 0) {
            return $this->result($open->membership_plan_id === $plan->id ? 'selected' : 'available', null);
        }

        if ($open->membership_plan_id === $plan->id) {
            return $this->result('selected', 'پرداخت این پلن در حال تکمیل است.');
        }

        return $plan->level > ($open->plan?->level ?? 0)
            ? $this->result('upgrade', null)
            : $this->result('blocked', 'برای پلن فعلی پرداخت ثبت شده است؛ فقط ارتقا ممکن است.');
    }

    /**
     * @return array{ok: bool, message: string, due: ?MembershipDue}
     */
    public function choose(MembershipPlan $plan, int $months): array
    {
        if (! $plan->offersTerm($months)) {
            return ['ok' => false, 'message' => 'این مدت برای پلن انتخابی فعال نیست.', 'due' => null];
        }

        $evaluation = $this->evaluate($plan);

        if (in_array($evaluation['state'], ['blocked', 'current'], true)) {
            return ['ok' => false, 'message' => (string) $evaluation['message'], 'due' => null];
        }

        $term = $plan->termPrice($months);
        $open = $this->openDue();
        $credit = $evaluation['state'] === 'upgrade' && $this->activeDue() !== null ? $this->upgradeCredit() : 0;

        $due = $open ?? new MembershipDue(['user_id' => $this->user->id]);
        $due->fill([
            'membership_plan_id' => $plan->id,
            'title' => 'حق عضویت '.$plan->name.' — '.$term['label'],
            'period' => (string) JalaliDate::currentYear(),
            'amount' => max(0, $term['final'] - $credit),
            'term_months' => $months,
            'discount_percent' => $term['discount'],
            'starts_on' => now()->toDateString(),
            'ends_on' => now()->addDays($term['days'])->toDateString(),
        ])->save();

        return ['ok' => true, 'message' => 'پلن «'.$plan->name.'» ('.$term['label'].') ثبت شد. پس از پرداخت و ثبت توسط مدیر، نشان سطح شما فعال می‌شود.', 'due' => $due];
    }

    /**
     * @param  'available'|'selected'|'current'|'upgrade'|'blocked'  $state
     * @return array{state: 'available'|'selected'|'current'|'upgrade'|'blocked', message: ?string}
     */
    private function result(string $state, ?string $message): array
    {
        return ['state' => $state, 'message' => $message];
    }
}
