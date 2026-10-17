<?php

namespace Tests\Feature;

use App\Enums\UserRole;
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
        $owner = User::factory()->create(['role' => UserRole::Club]);
        $club = Club::factory()->create(['owner_id' => $owner->id, 'name' => 'باشگاه نمونه', 'member_count' => 120]);
        Program::factory()->create(['club_id' => $club->id, 'title' => 'صعود قله نمونه']);
        Review::factory()->forReviewable($club)->approved()->create(['body' => 'باشگاه بسیار منظمی است.']);

        $this->actingAs($owner)
            ->get(route('panel.club.dashboard'))
            ->assertOk()
            ->assertSee('باشگاه نمونه', false)
            ->assertSee('اعضای باشگاه', false)
            ->assertSee('صعود قله نمونه', false)
            ->assertSee('باشگاه بسیار منظمی است.', false)
            ->assertSee('تکمیل پروفایل', false);
    }

    public function test_coach_and_shop_dashboards_render_with_profiles(): void
    {
        $coachUser = User::factory()->create(['role' => UserRole::Coach]);
        CoachProfile::factory()->create(['user_id' => $coachUser->id, 'headline' => 'مربی سنگ‌نوردی']);
        $shopUser = User::factory()->create(['role' => UserRole::Shop]);
        Shop::factory()->create(['user_id' => $shopUser->id, 'name' => 'فروشگاه نمونه']);

        $this->actingAs($coachUser)->get(route('panel.coach.dashboard'))
            ->assertOk()
            ->assertSee('مربی سنگ‌نوردی', false);

        $this->actingAs($shopUser)->get(route('panel.shop.dashboard'))
            ->assertOk()
            ->assertSee('فروشگاه نمونه', false);
    }

    public function test_role_without_profile_sees_guidance_instead_of_error(): void
    {
        $this->actingAs(User::factory()->create(['role' => UserRole::Leader]))
            ->get(route('panel.leader.dashboard'))
            ->assertOk()
            ->assertSee('هنوز ساخته نشده', false);
    }

    public function test_climber_dashboard_lists_own_reviews(): void
    {
        $climber = User::factory()->create(['role' => UserRole::Climber]);
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
        $this->actingAs(User::factory()->create(['role' => UserRole::Shop]))
            ->get(route('profile.edit'))
            ->assertOk()
            ->assertSee('پنل فروشگاه', false);
    }
}
