<?php

namespace Tests\Feature;

use App\Enums\DestinationCategory;
use App\Models\ClimbBadge;
use App\Models\Destination;
use App\Models\PeakAscent;
use App\Models\User;
use App\Support\PeakProgress;
use Database\Seeders\ClimbBadgeSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PeakLadderTest extends TestCase
{
    use RefreshDatabase;

    private function peak(string $name, int $elevation): Destination
    {
        return Destination::factory()->create(['name' => $name, 'elevation' => $elevation, 'category' => DestinationCategory::Peak]);
    }

    public function test_home_page_no_longer_shows_the_ladder_but_the_page_lists_peaks_tallest_first(): void
    {
        $this->peak('قله پایین‌تر', 3500);
        $this->peak('قله بلندتر', 4400);

        $this->get(route('home'))->assertOk()->assertDontSee('نردبان ارتفاع قله‌ها', false);

        $html = $this->get(route('peaks.index'))->assertOk()->getContent();

        $this->assertStringContainsString('نردبان ارتفاع قله‌ها', $html);
        $this->assertLessThan(strpos($html, 'قله پایین‌تر'), strpos($html, 'قله بلندتر'));
    }

    public function test_guests_see_a_login_prompt_and_no_tick_marks_or_leaderboard(): void
    {
        $this->peak('قله مهمان', 4000);
        ClimbBadge::factory()->create(['name' => 'نشان آزمایشی']);

        $this->get(route('peaks.index'))
            ->assertOk()
            ->assertSee('پیشرفت خود را دنبال کنید', false)
            ->assertSee('نشان آزمایشی', false)
            ->assertDontSee('قله‌نوردان پیشرو', false)
            ->assertDontSee('صعودنشده', false);
    }

    public function test_logged_in_climber_sees_ticks_progress_and_next_badge(): void
    {
        $user = User::factory()->create();
        $climbed = $this->peak('قله صعودشده', 4200);
        $this->peak('قله مانده', 3900);
        PeakAscent::factory()->create(['user_id' => $user->id, 'destination_id' => $climbed->id]);
        ClimbBadge::factory()->create(['name' => 'اولین تست', 'required_count' => 1, 'sort_order' => 1]);
        ClimbBadge::factory()->create(['name' => 'پنج‌قله تست', 'required_count' => 5, 'sort_order' => 2]);

        $this->actingAs($user)->get(route('peaks.index'))
            ->assertOk()
            ->assertSee('قله‌های صعودشده', false)
            ->assertSee('اولین تست', false)
            ->assertSee('هدف بعدی', false)
            ->assertSee('پنج‌قله تست', false)
            ->assertSee('۴ قله', false)
            ->assertSee('قله‌نوردان پیشرو', false)
            ->assertSee('صعودشده', false);
    }

    public function test_filter_shows_only_unclimbed_or_climbed_peaks_for_logged_in_users(): void
    {
        $user = User::factory()->create();
        $done = $this->peak('قله انجام‌شده', 4100);
        $this->peak('قله باقی‌مانده', 3800);
        PeakAscent::factory()->create(['user_id' => $user->id, 'destination_id' => $done->id]);

        $this->actingAs($user)->get(route('peaks.index', ['show' => 'todo']))
            ->assertSee('قله باقی‌مانده', false)
            ->assertDontSee('قله انجام‌شده', false);

        $this->actingAs($user)->get(route('peaks.index', ['show' => 'done']))
            ->assertSee('قله انجام‌شده', false)
            ->assertDontSee('قله باقی‌مانده', false);
    }

    public function test_guests_ignore_the_progress_filter(): void
    {
        $this->peak('قله باقی‌مانده', 3800);

        $this->get(route('peaks.index', ['show' => 'done']))->assertSee('قله باقی‌مانده', false);
    }

    public function test_badges_respect_count_and_minimum_elevation(): void
    {
        $user = User::factory()->create();
        $high = $this->peak('قله ۴۰۰۰', 4100);
        $low = $this->peak('قله ۳۰۰۰', 3100);
        PeakAscent::factory()->create(['user_id' => $user->id, 'destination_id' => $high->id]);
        PeakAscent::factory()->create(['user_id' => $user->id, 'destination_id' => $low->id]);

        $any = ClimbBadge::factory()->create(['required_count' => 2]);
        $four = ClimbBadge::factory()->create(['required_count' => 2, 'min_elevation' => 4000]);

        $badges = (new PeakProgress($user))->badges()->keyBy(fn (array $row): int => $row['badge']->id);

        $this->assertTrue($badges[$any->id]['earned']);
        $this->assertFalse($badges[$four->id]['earned']);
        $this->assertSame(1, $badges[$four->id]['current']);
        $this->assertSame(50, $badges[$four->id]['percent']);
        $this->assertSame($any->id, PeakProgress::title($badges->values())['badge']->id);
    }

    public function test_inactive_badges_are_hidden(): void
    {
        ClimbBadge::factory()->create(['name' => 'نشان خاموش', 'is_active' => false]);

        $this->get(route('peaks.index'))->assertDontSee('نشان خاموش', false);
    }

    public function test_admin_manages_badges_and_records_ascents_without_duplicates(): void
    {
        $admin = User::factory()->admin()->create();
        $climber = User::factory()->create();
        $peak = $this->peak('قله ثبت‌شده', 4300);

        $this->actingAs($admin)->post(route('panel.admin.resources.store', 'climb-badges'), [
            'name' => 'نشان ادمین', 'required_count' => 3, 'min_elevation' => 3500, 'color' => 'amber', 'icon' => 'trophy', 'is_active' => '1',
        ])->assertRedirect(route('panel.admin.resources.index', 'climb-badges'));
        $this->assertSame(3500, ClimbBadge::query()->firstOrFail()->min_elevation);

        $payload = ['user_id' => $climber->id, 'destination_id' => $peak->id, 'climbed_on' => '2026-09-01'];

        $this->actingAs($admin)->post(route('panel.admin.resources.store', 'peak-ascents'), $payload)
            ->assertRedirect(route('panel.admin.resources.index', 'peak-ascents'));
        $this->actingAs($admin)->post(route('panel.admin.resources.store', 'peak-ascents'), $payload)
            ->assertSessionHasErrors('destination_id');
        $this->assertSame(1, PeakAscent::query()->count());
    }

    public function test_footer_links_to_the_ladder_page(): void
    {
        $this->get(route('home'))->assertSee(route('peaks.index'), false);
    }

    public function test_seeder_is_idempotent(): void
    {
        $this->peak('قله نمونه ۱', 4000);
        User::factory()->create(['username' => 'climber']);

        $this->seed(ClimbBadgeSeeder::class);
        $this->seed(ClimbBadgeSeeder::class);

        $this->assertSame(6, ClimbBadge::query()->count());
        $this->assertSame(1, PeakAscent::query()->count());
    }
}
