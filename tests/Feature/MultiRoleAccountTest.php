<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Livewire\Panel\EditClubProfile;
use App\Livewire\Panel\EditShopProfile;
use App\Models\Club;
use App\Models\CoachProfile;
use App\Models\FinanceLedgerAccess;
use App\Models\MembershipPlan;
use App\Models\Shop;
use App\Models\User;
use App\Support\FinanceAccess;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class MultiRoleAccountTest extends TestCase
{
    use RefreshDatabase;

    private function personWithEverything(): User
    {
        $person = User::factory()->create(['name' => 'رضا چندنقشه']);
        Club::factory()->create(['owner_id' => $person->id, 'name' => 'باشگاه رضا']);
        Shop::factory()->create(['user_id' => $person->id, 'name' => 'فروشگاه رضا']);
        CoachProfile::factory()->create(['user_id' => $person->id, 'is_leader' => false, 'headline' => 'مربی رضا']);

        return $person;
    }

    public function test_roles_are_derived_from_what_the_account_owns(): void
    {
        $this->assertSame([UserRole::Climber], User::factory()->create()->roles()->all());
        $this->assertSame([UserRole::Admin], User::factory()->admin()->shop()->create()->roles()->all());
        $this->assertTrue(User::factory()->leader()->create()->hasRole(UserRole::Leader));
        $this->assertFalse(User::factory()->leader()->create()->hasRole(UserRole::Coach));

        $person = $this->personWithEverything();

        $this->assertSame(
            [UserRole::Club, UserRole::Coach, UserRole::Shop, UserRole::Climber],
            $person->roles()->all(),
        );
        $this->assertSame('باشگاه، مربی، فروشگاه', $person->roleLabel());
        $this->assertSame('پنل کاربری', $person->panelTitle());
    }

    public function test_role_is_picked_up_as_soon_as_a_profile_is_attached(): void
    {
        $user = User::factory()->create();
        $this->assertFalse($user->hasRole(UserRole::Shop));

        Shop::factory()->create(['user_id' => $user->id]);

        $this->assertTrue($user->forgetRoles()->hasRole(UserRole::Shop));
    }

    public function test_with_role_scope_and_counts_count_multi_role_accounts_under_each_role(): void
    {
        $this->personWithEverything();
        User::factory()->shop()->create();
        User::factory()->admin()->create();

        $counts = User::countsByRole();

        $this->assertSame(1, $counts['admin']);
        $this->assertSame(1, $counts['club']);
        $this->assertSame(1, $counts['coach']);
        $this->assertSame(2, $counts['shop']);
        $this->assertSame(2, $counts['climber']);
        $this->assertSame(2, User::query()->withRole(UserRole::Shop)->count());
    }

    public function test_login_lands_on_a_hub_showing_every_section(): void
    {
        $person = $this->personWithEverything();

        $this->actingAs($person)
            ->get(route('dashboard'))
            ->assertRedirect(route('panel.home'));

        $this->actingAs($person)
            ->get(route('panel.home'))
            ->assertOk()
            ->assertSee('باشگاه رضا', false)
            ->assertSee('فروشگاه رضا', false)
            ->assertSee('پروفایل مربی', false)
            ->assertSee('فعالیت و عضویت من', false);
    }

    public function test_every_section_of_a_multi_role_account_opens_with_its_own_data(): void
    {
        $person = $this->personWithEverything();
        $club = $person->ownedClubs()->firstOrFail();
        $shop = $person->shops()->firstOrFail();

        $this->actingAs($person)->get(route('panel.club.dashboard', $club))->assertOk()->assertSee('باشگاه رضا', false);
        $this->actingAs($person)->get(route('panel.shop.dashboard', $shop))->assertOk()->assertSee('فروشگاه رضا', false);
        $this->actingAs($person)->get(route('panel.coach.dashboard'))->assertOk()->assertSee('مربی رضا', false);
        $this->actingAs($person)->get(route('panel.climber.dashboard'))->assertOk();
        $this->actingAs($person)->get(route('panel.club.profile', $club))->assertOk();
        $this->actingAs($person)->get(route('panel.shop.profile', $shop))->assertOk();
        $this->actingAs($person)->get(route('panel.coach.profile'))->assertOk();
    }

    public function test_sidebar_lists_every_section_of_the_account(): void
    {
        $person = $this->personWithEverything();

        $this->actingAs($person)
            ->get(route('panel.climber.dashboard'))
            ->assertOk()
            ->assertSee('ویرایش باشگاه', false)
            ->assertSee('ویرایش فروشگاه', false)
            ->assertSee('پروفایل تخصصی', false)
            ->assertSee('پروفایل عمومی', false);
    }

    public function test_owner_with_two_clubs_can_switch_between_them(): void
    {
        $owner = User::factory()->create();
        $first = Club::factory()->create(['owner_id' => $owner->id, 'name' => 'باشگاه اول']);
        $second = Club::factory()->create(['owner_id' => $owner->id, 'name' => 'باشگاه دوم']);

        $this->actingAs($owner)->get(route('panel.club.dashboard'))
            ->assertRedirect(route('panel.club.dashboard', $first));

        $this->actingAs($owner)->get(route('panel.club.dashboard', $second))
            ->assertOk()
            ->assertSee('باشگاه دوم', false)
            ->assertSee(route('panel.club.dashboard', $first), false);
    }

    public function test_one_owner_cannot_open_or_edit_anothers_club_or_shop(): void
    {
        $stranger = $this->personWithEverything();
        $owner = User::factory()->create();
        $club = Club::factory()->create(['owner_id' => $owner->id]);
        $shop = Shop::factory()->create(['user_id' => $owner->id]);

        $this->actingAs($stranger)->get(route('panel.club.dashboard', $club))->assertForbidden();
        $this->actingAs($stranger)->get(route('panel.club.profile', $club))->assertForbidden();
        $this->actingAs($stranger)->get(route('panel.shop.dashboard', $shop))->assertForbidden();

        Livewire::actingAs($stranger)->test(EditClubProfile::class, ['club' => $club])->assertForbidden();
        Livewire::actingAs($stranger)->test(EditShopProfile::class, ['shop' => $shop])->assertForbidden();
    }

    public function test_sections_the_account_does_not_hold_redirect_to_its_own_panel(): void
    {
        $coach = User::factory()->coach()->create();

        $this->actingAs($coach)->get(route('panel.shop.dashboard'))->assertRedirect(route('panel.home'));
        $this->actingAs($coach)->get(route('panel.leader.dashboard'))->assertRedirect(route('panel.home'));
    }

    public function test_public_profile_shows_every_role_of_the_person(): void
    {
        $person = $this->personWithEverything();

        $this->get(route('profiles.show', $person))
            ->assertOk()
            ->assertSee('باشگاه رضا', false)
            ->assertSee('فروشگاه رضا', false)
            ->assertSee('باشگاه، مربی، فروشگاه', false);
    }

    public function test_membership_plans_cover_all_roles_of_the_account(): void
    {
        $person = $this->personWithEverything();
        MembershipPlan::factory()->create(['name' => 'پلن فروشگاه', 'role' => UserRole::Shop]);
        MembershipPlan::factory()->create(['name' => 'پلن باشگاه', 'role' => UserRole::Club]);
        MembershipPlan::factory()->create(['name' => 'پلن کوهنورد', 'role' => UserRole::Climber]);

        $this->actingAs($person)
            ->get(route('panel.membership'))
            ->assertOk()
            ->assertSee('پلن فروشگاه', false)
            ->assertSee('پلن باشگاه', false)
            ->assertDontSee('پلن کوهنورد', false);
    }

    public function test_ledger_group_grant_applies_to_any_role_of_the_account(): void
    {
        FinanceLedgerAccess::factory()->create(['role' => UserRole::Shop]);

        $this->assertTrue(FinanceAccess::canViewLedger($this->personWithEverything()));
        $this->assertFalse(FinanceAccess::canViewLedger(User::factory()->club()->create()));
    }

    public function test_admin_user_form_links_to_attaching_profiles_for_existing_user(): void
    {
        $admin = User::factory()->admin()->create();
        $person = $this->personWithEverything();

        $this->actingAs($admin)
            ->get(route('panel.admin.users.edit', $person))
            ->assertOk()
            ->assertSee('باشگاه رضا', false)
            ->assertSee('فروشگاه رضا', false)
            ->assertSee('owner_id='.$person->id, false);

        $this->actingAs($admin)
            ->get(route('panel.admin.resources.create', ['resource' => 'clubs', 'owner_id' => $person->id]))
            ->assertOk()
            ->assertSee('value="'.$person->id.'" selected', false);
    }

    public function test_admin_users_list_filters_by_role(): void
    {
        $admin = User::factory()->admin()->create();
        $this->personWithEverything();
        User::factory()->create(['name' => 'فقط کوهنورد عادی']);

        $this->actingAs($admin)
            ->get(route('panel.admin.users.index', ['role' => 'shop']))
            ->assertOk()
            ->assertSee('رضا چندنقشه', false)
            ->assertDontSee('فقط کوهنورد عادی', false);
    }
}
