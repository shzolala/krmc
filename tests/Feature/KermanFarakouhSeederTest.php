<?php

namespace Tests\Feature;

use App\Models\Club;
use App\Models\CoachProfile;
use Database\Seeders\KermanFarakouhSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KermanFarakouhSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_imports_kerman_clubs_and_coaches_from_json(): void
    {
        $this->seed(KermanFarakouhSeeder::class);

        $this->assertSame(38, Club::query()->whereNotNull('farakouh_id')->count());
        $this->assertSame(54, CoachProfile::query()->whereNotNull('farakouh_id')->count());

        $amavand = Club::query()->where('farakouh_id', 381)->first();
        $this->assertNotNull($amavand);
        $this->assertSame('باشگاه کوه نوردی اماوند کرمان', $amavand->name);
        $this->assertSame(347, $amavand->member_count);
        $this->assertSame(202, $amavand->program_count);
        $this->assertIsArray($amavand->courses_summary);

        $coach = CoachProfile::query()->where('farakouh_id', 807)->first();
        $this->assertNotNull($coach);
        $this->assertSame($amavand->id, $coach->club_id);
        $this->assertSame(68, $coach->total_courses_taught);
        $this->assertIsArray($coach->certificates);
        $this->assertNotEmpty($coach->certificates);
    }

    public function test_coaches_with_unknown_club_names_stay_unlinked(): void
    {
        $this->seed(KermanFarakouhSeeder::class);

        $this->assertSame(38, Club::query()->count());
        $this->assertSame(6, CoachProfile::query()->whereNull('club_id')->whereNotNull('farakouh_id')->count());
        $this->assertFalse(Club::query()->where('name', 'هیات کوه نوردی ارتش')->exists());
        $this->assertFalse(
            Club::query()->where('name', 'باشگاه کوه نوردی دوستداران طبیعت (نیچرفرند) تهران')->exists()
        );
    }
}
