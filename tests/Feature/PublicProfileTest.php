<?php

namespace Tests\Feature;

use App\Models\Club;
use App\Models\CoachProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_profile_is_available_by_username(): void
    {
        $user = User::factory()->create([
            'name' => 'کوهنورد نمونه',
            'username' => 'climber',
        ]);

        $this->get(route('profiles.show', $user))
            ->assertOk()
            ->assertSee('کوهنورد نمونه', false)
            ->assertSee('@climber', false)
            ->assertSee('کوهنورد', false);
    }

    public function test_coach_public_profile_links_to_coach_page(): void
    {
        $user = User::factory()->create([
            'username' => 'sara-mohammadi',
        ]);
        $profile = CoachProfile::factory()->create([
            'user_id' => $user->id,
            'slug' => 'sara-mohammadi',
            'specialty' => 'صخره‌نوردی',
        ]);

        $this->get('/u/sara-mohammadi')
            ->assertOk()
            ->assertSee('صخره‌نوردی', false)
            ->assertSee(route('coaches.show', $profile), false);
    }

    public function test_club_owner_public_profile_lists_owned_clubs(): void
    {
        $owner = User::factory()->create([
            'username' => 'club-kerman',
        ]);
        $club = Club::factory()->create([
            'owner_id' => $owner->id,
            'name' => 'باشگاه کوهنوردی کرمان',
        ]);

        $this->get(route('profiles.show', $owner))
            ->assertOk()
            ->assertSee('باشگاه کوهنوردی کرمان', false)
            ->assertSee(route('clubs.show', $club), false);
    }
}
