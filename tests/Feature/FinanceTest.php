<?php

namespace Tests\Feature;

use App\Enums\FundExpenseCategory;
use App\Enums\FundIncomeSource;
use App\Enums\PlanBadgeColor;
use App\Enums\UserRole;
use App\Models\FinanceLedgerAccess;
use App\Models\FundCampaign;
use App\Models\FundExpense;
use App\Models\FundIncome;
use App\Models\MembershipDue;
use App\Models\MembershipPlan;
use App\Models\User;
use App\Support\FinanceAccess;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_sees_own_dues_progress_and_fund_summary(): void
    {
        $member = User::factory()->club()->create();
        $due = MembershipDue::factory()->for($member)->create(['period' => '1405', 'amount' => 2_000_000]);
        FundIncome::factory()->create([
            'source' => FundIncomeSource::Membership,
            'amount' => 500_000,
            'user_id' => $member->id,
            'membership_due_id' => $due->id,
        ]);
        FundIncome::factory()->create(['source' => FundIncomeSource::Sponsorship, 'amount' => 1_500_000]);
        FundExpense::factory()->create(['category' => FundExpenseCategory::CoachTraining, 'amount' => 700_000]);

        $this->actingAs($member)
            ->get(route('panel.finance'))
            ->assertOk()
            ->assertSee(fa_money(1_300_000), false)
            ->assertSee(fa_money(1_500_000), false)
            ->assertSee('۲۵٪', false)
            ->assertSee('مربی‌پروری و آموزش', false);
    }

    public function test_ledger_detail_is_hidden_without_a_grant(): void
    {
        $member = User::factory()->shop()->create();
        FundExpense::factory()->create(['title' => 'خرید طناب ویژه', 'amount' => 100_000]);

        $this->actingAs($member)
            ->get(route('panel.finance'))
            ->assertOk()
            ->assertDontSee('خرید طناب ویژه', false);
    }

    public function test_ledger_detail_is_visible_to_granted_role_or_user_only(): void
    {
        FundExpense::factory()->create(['title' => 'خرید طناب ویژه']);
        FinanceLedgerAccess::factory()->create(['role' => UserRole::Coach]);
        $grantedUser = User::factory()->shop()->create();
        FinanceLedgerAccess::factory()->create(['role' => null, 'user_id' => $grantedUser->id]);

        $coach = User::factory()->coach()->create();
        $otherShop = User::factory()->shop()->create();
        $club = User::factory()->club()->create();

        $this->assertTrue(FinanceAccess::canViewLedger($coach));
        $this->assertTrue(FinanceAccess::canViewLedger($grantedUser));
        $this->assertTrue(FinanceAccess::canViewLedger(User::factory()->admin()->create()));
        $this->assertFalse(FinanceAccess::canViewLedger($otherShop));
        $this->assertFalse(FinanceAccess::canViewLedger($club));

        $this->actingAs($coach)->get(route('panel.finance'))->assertSee('خرید طناب ویژه', false);
        $this->actingAs($club)->get(route('panel.finance'))->assertDontSee('خرید طناب ویژه', false);
    }

    public function test_guest_is_redirected_and_non_admin_cannot_open_admin_finance(): void
    {
        $this->get(route('panel.finance'))->assertRedirect(route('login'));

        $this->actingAs(User::factory()->coach()->create())
            ->get(route('panel.admin.finance'))
            ->assertRedirect(route('panel.home'));
    }

    public function test_admin_overview_shows_ledger_and_campaign_progress(): void
    {
        FundCampaign::factory()->create(['title' => 'کمپین مربی‌پروری', 'goal_amount' => 1_000_000]);
        FundExpense::factory()->create(['title' => 'کارگاه مربیگری']);

        $this->actingAs(User::factory()->admin()->create())
            ->get(route('panel.admin.finance'))
            ->assertOk()
            ->assertSee('کمپین مربی‌پروری', false)
            ->assertSee('کارگاه مربیگری', false);
    }

    public function test_admin_records_membership_payment_and_it_links_to_the_member(): void
    {
        $admin = User::factory()->admin()->create();
        $member = User::factory()->club()->create();
        $due = MembershipDue::factory()->for($member)->create(['amount' => 1_000_000]);

        $this->actingAs($admin)
            ->post(route('panel.admin.resources.store', 'incomes'), [
                'source' => 'membership',
                'amount' => 400000,
                'received_at' => '2026-10-01',
                'membership_due_id' => $due->id,
            ])
            ->assertRedirect(route('panel.admin.resources.index', 'incomes'));

        $income = FundIncome::query()->firstOrFail();
        $this->assertSame($member->id, $income->user_id);
        $this->assertSame(40, $due->fresh()->paidPercent());
    }

    public function test_membership_income_requires_a_due(): void
    {
        $this->actingAs(User::factory()->admin()->create())
            ->post(route('panel.admin.resources.store', 'incomes'), [
                'source' => 'membership',
                'amount' => 1000,
                'received_at' => '2026-10-01',
            ])
            ->assertSessionHasErrors('membership_due_id');
    }

    public function test_admin_can_create_due_with_generated_title_and_grant_access(): void
    {
        $admin = User::factory()->admin()->create();
        $member = User::factory()->shop()->create(['name' => 'فروشگاه نمونه']);

        $this->actingAs($admin)
            ->post(route('panel.admin.resources.store', 'dues'), [
                'user_id' => $member->id,
                'period' => '1405',
                'amount' => 3000000,
            ])
            ->assertRedirect();

        $this->assertSame('حق عضویت 1405 — فروشگاه نمونه', MembershipDue::query()->firstOrFail()->title);

        $this->actingAs($admin)
            ->post(route('panel.admin.resources.store', 'ledger-access'), ['role' => 'coach'])
            ->assertRedirect();
        $this->assertSame(UserRole::Coach, FinanceLedgerAccess::query()->firstOrFail()->role);

        $this->actingAs($admin)
            ->post(route('panel.admin.resources.store', 'ledger-access'), [])
            ->assertSessionHasErrors(['role', 'user_id']);
    }

    public function test_date_fields_use_the_jalali_picker(): void
    {
        $this->actingAs(User::factory()->admin()->create())
            ->get(route('panel.admin.resources.create', 'expenses'))
            ->assertOk()
            ->assertSee('jalaliPicker', false)
            ->assertSee('name="spent_at"', false)
            ->assertDontSee('type="date"', false);
    }

    public function test_member_sees_only_active_plans_for_own_group_and_can_choose_one(): void
    {
        $club = User::factory()->club()->create();
        $general = MembershipPlan::factory()->create(['name' => 'پلن همگانی', 'price' => 1_000_000]);
        MembershipPlan::factory()->create(['name' => 'پلن مخصوص مربی', 'role' => UserRole::Coach]);
        $inactive = MembershipPlan::factory()->create(['name' => 'پلن غیرفعال', 'is_active' => false]);

        $this->actingAs($club)
            ->get(route('panel.membership'))
            ->assertOk()
            ->assertSee('پلن همگانی', false)
            ->assertDontSee('پلن مخصوص مربی', false)
            ->assertDontSee('پلن غیرفعال', false);

        $this->actingAs($club)->post(route('panel.membership.choose', $inactive), ['months' => 12])->assertNotFound();

        $this->actingAs($club)
            ->post(route('panel.membership.choose', $general), ['months' => 12])
            ->assertRedirect(route('panel.finance'));

        $due = $club->membershipDues()->firstOrFail();
        $this->assertSame($general->id, $due->membership_plan_id);
        $this->assertSame(1_000_000, $due->amount);
    }

    public function test_member_cannot_choose_plan_of_another_group_or_switch_after_paying(): void
    {
        $club = User::factory()->club()->create();
        $coachPlan = MembershipPlan::factory()->create(['role' => UserRole::Coach]);
        $plan = MembershipPlan::factory()->create();

        $this->actingAs($club)->post(route('panel.membership.choose', $coachPlan), ['months' => 12])->assertNotFound();

        $this->actingAs($club)->post(route('panel.membership.choose', $plan), ['months' => 12]);
        $due = $club->membershipDues()->firstOrFail();
        FundIncome::factory()->create(['source' => FundIncomeSource::Membership, 'amount' => 100_000, 'user_id' => $club->id, 'membership_due_id' => $due->id]);

        $other = MembershipPlan::factory()->create(['price' => 9_000_000]);
        $this->actingAs($club)->post(route('panel.membership.choose', $other), ['months' => 12])->assertSessionHas('status');
        $this->assertSame($plan->id, $due->fresh()->membership_plan_id);
    }

    public function test_badge_requires_settled_dues_and_a_logged_in_viewer(): void
    {
        $plan = MembershipPlan::factory()->create([
            'badge_label' => 'حامی طلایی',
            'badge_color' => PlanBadgeColor::Amber,
            'price' => 1_000_000,
        ]);
        $member = User::factory()->coach()->create();
        $due = MembershipDue::factory()->for($member)->create(['membership_plan_id' => $plan->id, 'amount' => 1_000_000]);
        $viewer = User::factory()->create();

        $this->assertNull($member->fresh()->currentMembershipPlan());

        FundIncome::factory()->create(['source' => FundIncomeSource::Membership, 'amount' => 1_000_000, 'user_id' => $member->id, 'membership_due_id' => $due->id]);
        $this->assertSame($plan->id, $member->fresh()->currentMembershipPlan()?->id);

        $this->actingAs($viewer)->get(route('profiles.show', $member))->assertSee('حامی طلایی', false);
        auth()->logout();
        $this->get(route('profiles.show', $member))->assertDontSee('حامی طلایی', false);

        $this->actingAs($viewer)->get(route('panel.finance'))->assertSee('حامیان صندوق', false)->assertSee('حامی طلایی', false);
    }

    public function test_admin_can_define_plan_with_features_and_dues_take_plan_price(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->post(route('panel.admin.resources.store', 'plans'), [
                'name' => 'پلن نقره‌ای',
                'role' => 'shop',
                'price' => 3000000,
                'level' => 2,
                'features' => "نشان ویژه\nمعرفی در خبرنامه",
                'badge_color' => 'sky',
                'badge_icon' => 'shield-check',
                'is_active' => '1',
            ])
            ->assertRedirect(route('panel.admin.resources.index', 'plans'));

        $plan = MembershipPlan::query()->firstOrFail();
        $this->assertSame(['نشان ویژه', 'معرفی در خبرنامه'], $plan->features);
        $this->assertSame(UserRole::Shop, $plan->role);

        $member = User::factory()->shop()->create();
        $this->actingAs($admin)
            ->post(route('panel.admin.resources.store', 'dues'), ['user_id' => $member->id, 'period' => '1405', 'membership_plan_id' => $plan->id])
            ->assertRedirect();
        $this->assertSame(3_000_000, MembershipDue::query()->firstOrFail()->amount);
    }
}
