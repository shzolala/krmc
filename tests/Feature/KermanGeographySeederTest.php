<?php

namespace Tests\Feature;

use App\Enums\DestinationCategory;
use App\Models\Attraction;
use App\Models\Destination;
use App\Models\MountainRegion;
use Database\Seeders\KermanGeographySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KermanGeographySeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_imports_regions_peaks_and_attractions_from_json(): void
    {
        $this->seed(KermanGeographySeeder::class);

        $this->assertSame(16, MountainRegion::query()->count());
        $this->assertSame(88, Destination::query()->whereNotNull('catalog_id')->count());
        $this->assertSame(48, Destination::query()->where('category', DestinationCategory::Peak)->count());
        $this->assertSame(40, Destination::query()->where('category', DestinationCategory::NatureSpot)->count());
        $this->assertSame(45, Attraction::query()->count());

        $hezarRegion = MountainRegion::query()->where('catalog_id', 'reg-hezar')->first();
        $this->assertNotNull($hezarRegion);
        $this->assertSame('کوهستان هزار', $hezarRegion->name);
        $this->assertSame('hezar-hazaran-massif', $hezarRegion->slug);
        $this->assertSame(4501, $hezarRegion->highest_elevation);

        $hezarPeak = Destination::query()->where('catalog_id', 'peak-hezar')->first();
        $this->assertNotNull($hezarPeak);
        $this->assertSame($hezarRegion->id, $hezarPeak->mountain_region_id);
        $this->assertSame('mount-hezar', $hezarPeak->slug);
        $this->assertSame(4501, $hezarPeak->elevation);
        $this->assertIsArray($hezarPeak->trail_info);
        $this->assertNotEmpty($hezarPeak->trail_info);

        $garden = Attraction::query()->where('catalog_id', 'att-bagh-shahzadeh')->first();
        $this->assertNotNull($garden);
        $this->assertSame('shazdeh-garden-mahan', $garden->slug);
        $this->assertTrue($garden->isUnesco());
    }

    public function test_it_links_nearby_attractions_and_related_destinations(): void
    {
        $this->seed(KermanGeographySeeder::class);

        $hezar = Destination::query()->where('catalog_id', 'peak-hezar')->firstOrFail();

        $this->assertTrue(
            $hezar->attractions()->where('catalog_id', 'att-arg-rayen')->exists()
        );
        $this->assertTrue(
            $hezar->relatedDestinations()->where('catalog_id', 'nat-rayen-waterfall')->exists()
        );
    }

    public function test_geography_directory_pages_are_reachable(): void
    {
        $this->seed(KermanGeographySeeder::class);

        $this->get(route('regions.index'))->assertOk();
        $this->get(route('regions.show', 'hezar-hazaran-massif'))->assertOk()->assertSee('کوهستان هزار', false);
        $this->get(route('destinations.show', 'mount-hezar'))->assertOk()->assertSee('قله هزار', false);
        $this->get(route('attractions.index'))->assertOk();
        $this->get(route('attractions.show', 'shazdeh-garden-mahan'))->assertOk()->assertSee('باغ شاهزاده ماهان', false);
    }
}
