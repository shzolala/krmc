<?php

namespace Tests\Feature;

use App\Models\Accommodation;
use App\Models\Club;
use App\Models\CoachProfile;
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
        $response->assertSee(fa_num(Club::query()->whereNotNull('farakouh_id')->count()), false);
        $response->assertSee(fa_num(CoachProfile::query()->whereNotNull('farakouh_id')->count()), false);
        $response->assertSee(fa_num(Program::query()->ongoing()->count()), false);
        $response->assertSee('برنامه در حال برگزاری', false);
        $response->assertSee('پناهگاه‌های کرمان', false);
        $response->assertSee('اقامتگاه‌ها', false);
        $response->assertSee('مربیان و سرپرستان', false);
        $response->assertSee('انتخاب باشگاه‌های برجسته کوهنوردی', false);
        $response->assertSee('مشاهده همه باشگاه‌ها', false);
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
}
