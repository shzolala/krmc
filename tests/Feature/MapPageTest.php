<?php

namespace Tests\Feature;

use App\Models\Destination;
use App\Models\ProgramReport;
use App\Models\Shelter;
use App\Support\MapCatalog;
use Database\Seeders\PlatformDemoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class MapPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_map_page_renders_explorer_with_catalog_layers(): void
    {
        $this->seed(PlatformDemoSeeder::class);

        $response = $this->get(route('map'));

        $response->assertOk();
        $response->assertSee('نقشه یکپارچه کوهنوردی کرمان', false);
        $response->assertSee('data-map-explorer', false);
        $response->assertSee('قله‌ها و طبیعت', false);
        $response->assertSee('اقامت', false);
        $response->assertSee('پناهگاه‌ها', false);
        $response->assertSee('برنامه‌های اخیر', false);
        $response->assertSee('باشگاه‌ها', false);
        $response->assertSee('تجهیزات', false);
        $response->assertSee('هزار', false);
        $response->assertSee('dir="ltr"', false);
        $response->assertSee('مشاهده نقشه', false);
        $response->assertSee('مکان‌های داخل محدودهٔ نقشه', false);
        $response->assertSee('data-map-list', false);
        $response->assertSee('data-map-search', false);
    }

    public function test_map_catalog_includes_program_markers_from_destinations(): void
    {
        $this->seed(PlatformDemoSeeder::class);

        $destination = Destination::query()->where('slug', 'hezar')->firstOrFail();

        ProgramReport::query()->create([
            'title' => 'صعود آزمایشی هزار',
            'slug' => 'test-hezar-ascent',
            'excerpt' => 'برنامه آزمایشی برای نقشه',
            'body' => 'متن گزارش',
            'destination_id' => $destination->id,
            'published_at' => now()->subDay(),
        ]);

        $catalog = app(MapCatalog::class)->build();

        $this->assertGreaterThan(0, $catalog['counts']['destination']);
        $this->assertGreaterThan(0, $catalog['counts']['accommodation']);
        $this->assertGreaterThan(0, $catalog['counts']['program']);
        $this->assertTrue(
            $catalog['markers']->contains(
                fn (array $marker): bool => $marker['type'] === 'program'
                    && str_contains($marker['label'], 'صعود آزمایشی هزار')
            )
        );
    }

    public function test_cached_catalog_is_cleared_when_a_place_changes(): void
    {
        $this->seed(PlatformDemoSeeder::class);
        Cache::flush();

        $catalog = app(MapCatalog::class);
        $shelter = Shelter::query()->withCoordinates()->firstOrFail();

        $oldName = $shelter->name;

        $this->assertTrue($catalog->cached()['markers']->contains('label', $oldName));

        $shelter->update(['name' => 'پناهگاه تغییرنام‌یافته']);

        $markers = $catalog->cached()['markers'];
        $this->assertTrue($markers->contains('label', 'پناهگاه تغییرنام‌یافته'));
        $this->assertFalse($markers->contains('label', $oldName));
    }

    public function test_cached_catalog_keeps_markers_usable_on_second_request(): void
    {
        $this->seed(PlatformDemoSeeder::class);
        Cache::flush();

        $this->get(route('map'))->assertOk();
        $response = $this->get(route('map'));

        $response->assertOk();
        $response->assertDontSee('Illuminate\\Support\\Collection', false);
        $response->assertSee('"type":"shelter"', false);
    }
}
