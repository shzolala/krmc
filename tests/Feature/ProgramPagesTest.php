<?php

namespace Tests\Feature;

use App\Enums\ProgramStatus;
use App\Models\Club;
use App\Models\CoachProfile;
use App\Models\Destination;
use App\Models\MountainRegion;
use App\Models\Program;
use App\Models\User;
use Database\Seeders\PlatformDemoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProgramPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_programs_index_and_show_are_reachable(): void
    {
        $this->seed(PlatformDemoSeeder::class);

        $this->get(route('programs.index'))->assertOk();
        $this->get(route('programs.index', ['status' => 'ongoing']))->assertOk()->assertSee('در حال برگزاری', false);
        $this->get(route('programs.show', 'program-hezar-day'))->assertOk()->assertSee('صعود یک‌روزه هزار', false);
    }

    public function test_program_show_displays_schedule_range(): void
    {
        $this->seed(PlatformDemoSeeder::class);

        $this->get(route('programs.show', 'program-hezar-day'))
            ->assertOk()
            ->assertSee('زمان برگزاری', false)
            ->assertSee('aria-label="روزهای برنامه"', false);
    }

    public function test_program_requires_destination_and_club_while_leader_is_optional(): void
    {
        $destination = Destination::factory()->create();
        $club = Club::factory()->create();

        $withoutLeader = Program::factory()->ongoing()->create([
            'destination_id' => $destination->id,
            'club_id' => $club->id,
            'leader_id' => null,
            'title' => 'برنامه بدون سرپرست',
            'slug' => 'program-no-leader',
        ]);

        $leader = CoachProfile::factory()->create([
            'user_id' => User::factory()->create()->id,
            'club_id' => $club->id,
            'is_leader' => true,
        ]);

        $withLeader = Program::factory()->upcoming()->create([
            'destination_id' => $destination->id,
            'club_id' => $club->id,
            'leader_id' => $leader->id,
            'title' => 'برنامه با سرپرست',
            'slug' => 'program-with-leader',
        ]);

        $this->assertNull($withoutLeader->fresh()->leader_id);
        $this->assertSame($leader->id, $withLeader->fresh()->leader_id);
        $this->assertSame(ProgramStatus::Ongoing, $withoutLeader->status);

        $this->get(route('clubs.show', $club))
            ->assertOk()
            ->assertSee('برنامه بدون سرپرست', false)
            ->assertSee('برنامه با سرپرست', false);

        $this->get(route('coaches.show', $leader))
            ->assertOk()
            ->assertSee('برنامه با سرپرست', false)
            ->assertDontSee('برنامه بدون سرپرست', false);

        $this->get(route('destinations.show', $destination))
            ->assertOk()
            ->assertSee('برنامه‌های این مقصد', false)
            ->assertSee('برنامه بدون سرپرست', false);
    }

    public function test_home_hero_links_to_clubs_coaches_and_ongoing_programs(): void
    {
        $this->seed(PlatformDemoSeeder::class);

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('href="'.route('clubs.index').'"', false);
        $response->assertSee('href="'.route('coaches.index').'"', false);
        $response->assertSee('href="'.route('programs.index', ['status' => 'ongoing']).'"', false);
        $response->assertSee('برنامه در حال برگزاری', false);
        $response->assertSee(fa_num(Program::query()->ongoing()->count()), false);
    }

    public function test_programs_can_be_filtered_by_difficulty_region_club_and_capacity(): void
    {
        $region = MountainRegion::factory()->create(['slug' => 'test-region']);
        $hard = Destination::factory()->create(['mountain_region_id' => $region->id, 'trail_info' => ['difficulty' => ['grade_1_5' => 5]]]);
        $easy = Destination::factory()->create(['trail_info' => ['difficulty' => ['grade_1_5' => 1]]]);
        $club = Club::factory()->create(['slug' => 'test-club']);

        Program::factory()->create(['title' => 'صعود سخت', 'destination_id' => $hard->id, 'club_id' => $club->id, 'capacity' => 10, 'registered_count' => 3]);
        Program::factory()->create(['title' => 'پیاده‌روی آسان', 'destination_id' => $easy->id, 'capacity' => 10, 'registered_count' => 10]);

        $titles = fn (array $query) => $this->get(route('programs.index', $query))
            ->assertOk()
            ->viewData('programs')
            ->pluck('title')
            ->all();

        $this->assertSame(['صعود سخت'], $titles(['difficulty' => 'hard']));
        $this->assertSame(['پیاده‌روی آسان'], $titles(['difficulty' => 'easy']));
        $this->assertSame(['صعود سخت'], $titles(['region' => 'test-region']));
        $this->assertSame(['صعود سخت'], $titles(['club' => 'test-club']));
        $this->assertSame(['صعود سخت'], $titles(['available' => 1]));
        $this->assertCount(2, $titles(['difficulty' => 'unknown']));
    }
}
