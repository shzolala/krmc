<?php

namespace Tests\Feature;

use App\Enums\FundIncomeSource;
use App\Enums\UserRole;
use App\Models\FundIncome;
use App\Models\MembershipDue;
use App\Models\MembershipPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MembershipPlanChangeTest extends TestCase
{
    use RefreshDatabase;

    private function pay(User $user, MembershipDue $due, ?int $amount = null): void
    {
        FundIncome::factory()->create([
            'source' => FundIncomeSource::Membership,
            'amount' => $amount ?? $due->amount,
            'user_id' => $user->id,
            'membership_due_id' => $due->id,
        ]);
    }

    private function plan(int $level, int $price, array $attributes = []): MembershipPlan
    {
        return MembershipPlan::factory()->create(['role' => UserRole::Shop, 'level' => $level, 'price' => $price, ...$attributes]);
    }

    public function test_term_prices_scale_from_the_base_price_with_discounts(): void
    {
        $plan = $this->plan(1, 12_000_000, [
            'allow_monthly' => true,
            'allow_semiannual' => true,
            'discount_semiannual' => 5,
            'discount_annual' => 10,
        ]);

        $this->assertSame([1, 6, 12], $plan->offeredTerms());
        $this->assertSame(986_000, $plan->termPrice(1)['final']);
        $this->assertSame(5_622_000, $plan->termPrice(6)['final']);
        $this->assertSame(10_800_000, $plan->termPrice(12)['final']);
        $this->assertSame(12_000_000, $plan->termPrice(12)['list']);
    }

    public function test_plan_defaults_to_annual_when_no_term_is_enabled(): void
    {
        $plan = $this->plan(1, 1_000_000, ['allow_annual' => false]);

        $this->assertSame([12], $plan->offeredTerms());
    }

    public function test_member_cannot_pick_a_term_the_plan_does_not_offer(): void
    {
        $shop = User::factory()->shop()->create();
        $plan = $this->plan(1, 2_000_000, ['allow_semiannual' => true]);

        $this->actingAs($shop)->post(route('panel.membership.choose', $plan), ['months' => 1])
            ->assertSessionHas('status', 'این مدت برای پلن انتخابی فعال نیست.');
        $this->assertSame(0, MembershipDue::query()->count());

        $this->actingAs($shop)->post(route('panel.membership.choose', $plan), ['months' => 6])->assertRedirect(route('panel.finance'));
        $due = MembershipDue::query()->firstOrFail();
        $this->assertSame(6, $due->term_months);
        $this->assertSame($plan->termPrice(6)['final'], $due->amount);
        $this->assertTrue($due->ends_on->isSameDay(now()->addDays(180)));

        $this->actingAs($shop)->post(route('panel.membership.choose', $plan), ['months' => 5])->assertSessionHasErrors('months');
    }

    public function test_unpaid_choice_can_be_changed_freely_without_creating_duplicates(): void
    {
        $shop = User::factory()->shop()->create();
        $high = $this->plan(3, 10_000_000);
        $low = $this->plan(1, 2_000_000);

        $this->actingAs($shop)->post(route('panel.membership.choose', $high), ['months' => 12]);
        $this->actingAs($shop)->post(route('panel.membership.choose', $low), ['months' => 12]);

        $this->assertSame(1, MembershipDue::query()->count());
        $this->assertSame($low->id, MembershipDue::query()->firstOrFail()->membership_plan_id);
    }

    public function test_active_member_can_only_upgrade_and_gets_prorated_credit(): void
    {
        $shop = User::factory()->shop()->create();
        $silver = $this->plan(2, 5_000_000);
        $gold = $this->plan(3, 10_000_000);
        $basic = $this->plan(1, 2_000_000);

        $due = MembershipDue::factory()->for($shop)->create([
            'membership_plan_id' => $silver->id,
            'amount' => 5_000_000,
            'starts_on' => now()->subDays(100)->toDateString(),
            'ends_on' => now()->addDays(265)->toDateString(),
        ]);
        $this->pay($shop, $due);

        $this->actingAs($shop)->post(route('panel.membership.choose', $basic), ['months' => 12])
            ->assertSessionHas('status');
        $this->actingAs($shop)->post(route('panel.membership.choose', $silver), ['months' => 12])
            ->assertSessionHas('status');
        $this->assertSame(1, MembershipDue::query()->count());

        $this->actingAs($shop)->post(route('panel.membership.choose', $gold), ['months' => 12])
            ->assertRedirect(route('panel.finance'));

        $upgrade = MembershipDue::query()->where('membership_plan_id', $gold->id)->firstOrFail();
        $this->assertLessThan(10_000_000, $upgrade->amount);
        $this->assertGreaterThan(5_000_000, $upgrade->amount);
        $this->assertSame($silver->id, $shop->fresh()->currentMembershipPlan()?->id);

        $this->pay($shop, $upgrade);
        $this->assertSame($gold->id, $shop->fresh()->currentMembershipPlan()?->id);
    }

    public function test_level_cannot_go_back_even_after_a_membership_expires(): void
    {
        $shop = User::factory()->shop()->create();
        $silver = $this->plan(2, 5_000_000);
        $basic = $this->plan(1, 2_000_000);

        $due = MembershipDue::factory()->for($shop)->create([
            'membership_plan_id' => $silver->id,
            'amount' => 5_000_000,
            'starts_on' => now()->subDays(400)->toDateString(),
            'ends_on' => now()->subDays(35)->toDateString(),
        ]);
        $this->pay($shop, $due);

        $this->assertNull($shop->fresh()->currentMembershipPlan());

        $this->actingAs($shop)->post(route('panel.membership.choose', $basic), ['months' => 12])
            ->assertSessionHas('status', 'بازگشت به سطح پایین‌تر از سطح قبلی شما ممکن نیست.');
        $this->assertSame(1, MembershipDue::query()->count());

        $this->actingAs($shop)->post(route('panel.membership.choose', $silver), ['months' => 12])
            ->assertRedirect(route('panel.finance'));
        $this->assertSame(2, MembershipDue::query()->count());
    }

    public function test_plans_page_shows_level_and_term_selector(): void
    {
        $shop = User::factory()->shop()->create();
        $this->plan(2, 5_000_000, ['name' => 'فروشگاه حامی تست', 'allow_semiannual' => true, 'discount_annual' => 10]);

        $this->actingAs($shop)->get(route('panel.membership'))
            ->assertOk()
            ->assertSee('فروشگاه حامی تست', false)
            ->assertSee('سطح ۲', false)->assertDontSee('از ۳', false)
            ->assertSee('شش‌ماهه', false)
            ->assertSee('۱۰٪ تخفیف', false)
            ->assertDontSee('یک‌ماهه', false);
    }

    public function test_public_pricing_page_lists_plans_per_group_and_is_linked_from_footer(): void
    {
        $this->plan(1, 2_400_000, ['name' => 'فروشگاه همکار تست', 'allow_monthly' => true]);
        MembershipPlan::factory()->create(['name' => 'پلن بسته‌شده', 'role' => UserRole::Shop, 'is_active' => false]);

        $this->get(route('pricing'))
            ->assertOk()
            ->assertSee('فروشگاه همکار تست', false)
            ->assertSee('یک‌ماهه', false)
            ->assertSee('۲,۴۰۰,۰۰۰', false)
            ->assertDontSee('پلن بسته‌شده', false)
            ->assertSee('غیرفعال', false);

        $this->get(route('home'))->assertSee(route('pricing'), false);
    }
}
