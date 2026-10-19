<?php

namespace Tests\Feature;

use App\Models\Club;
use App\Models\CoachProfile;
use App\Models\Program;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Review\Models\Review;
use Tests\TestCase;

class RoleDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_club_dashboard_shows_club_stats_programs_and_reviews(): void
    {
        $owner = User::factory()->create();
        $club = Club::factory()->create(['owner_id' => $owner->id, 'name' => 'باشگاه نمونه', 'member_count' => 120]);
        Program::factory()->create(['club_id' => $club->id, 'title' => 'صعود قله نمونه']);
        Review::factory()->forReviewable($club)->approved()->create(['body' => 'باشگاه بسیار منظمی است.']);

        $this->actingAs($owner)
            ->get(route('panel.club.dashboard', $club))
            ->assertOk()
            ->assertSee('باشگاه نمونه', false)
            ->assertSee('اعضای باشگاه', false)
            ->assertSee('صعود قله نمونه', false)
            ->assertSee('باشگاه بسیار منظمی است.', false)
            ->assertSee('تکمیل پروفایل', false);
    }

    public function test_coach_and_shop_dashboards_render_with_profiles(): void
    {
        $coachUser = User::factory()->create();
        CoachProfile::factory()->create(['user_id' => $coachUser->id, 'is_leader' => false, 'headline' => 'مربی سنگ‌نوردی']);
        $shopUser = User::factory()->create();
        $shop = Shop::factory()->create(['user_id' => $shopUser->id, 'name' => 'فروشگاه نمونه']);

        $this->actingAs($coachUser)->get(route('panel.coach.dashboard'))
            ->assertOk()
            ->assertSee('مربی سنگ‌نوردی', false);

        $this->actingAs($shopUser)->get(route('panel.shop.dashboard', $shop))
            ->assertOk()
            ->assertSee('فروشگاه نمونه', false);
    }

    public function test_club_dashboard_without_a_club_redirects_to_the_users_own_panel(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('panel.club.dashboard'))
            ->assertRedirect(route('panel.climber.dashboard'));
    }

    public function test_climber_dashboard_lists_own_reviews(): void
    {
        $climber = User::factory()->create();
        Review::factory()->forReviewable(Club::factory()->create())->pending()->create([
            'user_id' => $climber->id,
            'body' => 'نظر من درباره باشگاه.',
        ]);

        $this->actingAs($climber)
            ->get(route('panel.climber.dashboard'))
            ->assertOk()
            ->assertSee('نظرات من', false)
            ->assertSee('نظر من درباره باشگاه.', false);
    }

    public function test_settings_pages_use_panel_layout_for_non_admins(): void
    {
        $this->actingAs(User::factory()->shop()->create())
            ->get(route('profile.edit'))
            ->assertOk()
            ->assertSee('پنل فروشگاه', false);
    }
}
