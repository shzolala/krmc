<?php

namespace Tests\Feature;

use App\Enums\ProgramStatus;
use App\Models\Accommodation;
use App\Models\Club;
use App\Models\Destination;
use App\Models\Program;
use App\Models\Shelter;
use App\Models\Shop;
use Database\Seeders\PlatformDemoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_renders_platform_overview(): void
    {
        $this->seed(PlatformDemoSeeder::class);

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('جامعه کوهنوردان کرمان', false);
        $response->assertSee('باشگاه کوه نوردی اماوند کرمان', false);
        $response->assertSee('هزار', false);
        $response->assertDontSee('باشگاه فعال', false);
        $response->assertSee(fa_num(Program::query()->ongoing()->count()), false);
        $response->assertSee('برنامه در حال برگزاری', false);
        $response->assertSee('پناهگاه‌های کرمان', false);
        $response->assertSee('اقامتگاه‌ها', false);
        $response->assertSee('مربیان و سرپرستان', false);
        $response->assertSee('باشگاه‌ها و سرپرستان تأییدشده', false);
        $response->assertSee('همه باشگاه‌ها', false);
        $response->assertDontSee('نردبان ارتفاع قله‌ها', false);
        $response->assertDontSee('سطح شما کدام است؟', false);
        $response->assertDontSee('مقصدتان را فیلتر کنید', false);
        $response->assertDontSee('جاذبه گردشگری', false);
    }

    public function test_destinations_can_be_filtered_by_difficulty_level(): void
    {
        $this->seed(PlatformDemoSeeder::class);

        $response = $this->get(route('destinations.index', ['difficulty' => 'hard']));

        $response->assertOk();
        $response->assertViewHas('destinations', function ($destinations): bool {
            return $destinations->count() > 0
                && $destinations->every(fn (Destination $destination): bool => in_array($destination->difficultyGrade(), [4, 5], true));
        });
    }

    public function test_unknown_difficulty_level_is_ignored(): void
    {
        $this->seed(PlatformDemoSeeder::class);

        $this->get(route('destinations.index', ['difficulty' => 'impossible']))
            ->assertOk()
            ->assertViewHas('activeDifficulty', null);
    }

    public function test_directory_pages_are_reachable(): void
    {
        $this->seed(PlatformDemoSeeder::class);

        $this->get(route('clubs.index'))->assertOk();
        $this->get(route('coaches.index'))->assertOk();
        $this->get(route('programs.index'))->assertOk();
        $this->get(route('programs.show', 'program-hezar-day'))->assertOk();
        $this->get(route('regions.index'))->assertOk();
        $this->get(route('destinations.index'))->assertOk();
        $this->get(route('attractions.index'))->assertOk();
        $this->get(route('accommodations.index'))->assertOk();
        $this->get(route('shelters.index'))->assertOk();
        $this->get(route('shops.index'))->assertOk();
        $this->get(route('map'))->assertOk();
        $this->get(route('clubs.show', 'club-381'))->assertOk();
        $this->get(route('regions.show', 'hezar-hazaran-massif'))->assertOk();
        $this->get(route('destinations.show', 'mount-hezar'))->assertOk();
        $this->get(route('attractions.show', 'shazdeh-garden-mahan'))->assertOk();
        $this->get(route('accommodations.show', 'eco-hezar'))->assertOk();
        $this->get(route('shelters.show', 'shelter-hezar'))->assertOk();
        $this->get(route('coaches.show', 'coach-807'))->assertOk();
        $this->get(route('coaches.show', 'ali-rezaei'))->assertOk();
    }

    public function test_entities_have_geo_coordinates_for_map(): void
    {
        $this->seed(PlatformDemoSeeder::class);

        $this->assertDatabaseHas('destinations', [
            'slug' => 'mount-hezar',
            'catalog_id' => 'peak-hezar',
        ]);

        $this->assertNotNull(Destination::query()->where('slug', 'mount-hezar')->value('latitude'));
        $this->assertNotNull(Destination::query()->where('catalog_id', 'peak-hezar')->value('mountain_region_id'));
        $this->assertNotNull(Club::query()->where('slug', 'club-381')->value('longitude'));
        $this->assertNotNull(Shop::query()->where('slug', 'kouhestan-kerman')->value('latitude'));
        $this->assertNotNull(Accommodation::query()->where('slug', 'eco-hezar')->value('longitude'));
        $this->assertNotNull(Shelter::query()->where('slug', 'shelter-joopar')->value('latitude'));
    }

    public function test_home_page_lists_ongoing_then_soonest_upcoming_programs_with_capacity(): void
    {
        Program::factory()->create(['title' => 'برنامه دیرتر', 'starts_at' => now()->addDays(20), 'capacity' => 20, 'registered_count' => 3, 'status' => ProgramStatus::Upcoming]);
        Program::factory()->create(['title' => 'برنامه زودتر', 'starts_at' => now()->addDays(4), 'capacity' => 20, 'registered_count' => 18, 'status' => ProgramStatus::Upcoming]);
        Program::factory()->ongoing()->create(['title' => 'برنامه در جریان']);
        Program::factory()->create(['title' => 'برنامه برگزارشده', 'status' => ProgramStatus::Completed]);

        $html = $this->get(route('home'))->assertOk()->getContent();

        $this->assertStringContainsString('برنامه‌های پیش‌رو', $html);
        $this->assertStringContainsString('۲ جای خالی', $html);
        $this->assertStringNotContainsString('برنامه برگزارشده', $html);
        $this->assertLessThan(strpos($html, 'برنامه زودتر'), strpos($html, 'برنامه در جریان'));
        $this->assertLessThan(strpos($html, 'برنامه دیرتر'), strpos($html, 'برنامه زودتر'));
    }

    public function test_home_page_groups_regions_attractions_and_accommodations_in_tabs_below_the_map(): void
    {
        $this->seed(PlatformDemoSeeder::class);

        $html = $this->get(route('home'))->assertOk()->getContent();

        $this->assertStringContainsString('مناطق، جاذبه‌ها و اقامتگاه‌ها', $html);
        $this->assertStringContainsString('tab-explore-regions', $html);
        $this->assertStringContainsString('tab-explore-attractions', $html);
        $this->assertStringContainsString('tab-explore-accommodations', $html);
        $this->assertGreaterThan(strpos($html, 'مسیرتان را قبل از حرکت ببینید'), strpos($html, 'tab-explore-regions'));
        $this->assertStringNotContainsString('باشگاه، مربی یا عضو پنل هستید', $html);
    }
}
