<?php

namespace Tests\Feature;

use App\Models\ShortLink;
use App\Models\User;
use App\Support\MapLink;
use Database\Seeders\PlatformDemoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MapLinkTest extends TestCase
{
    use RefreshDatabase;

    public function test_link_builder_only_includes_given_options(): void
    {
        $this->assertSame(route('map'), MapLink::url());

        $url = MapLink::url([
            'types' => ['shelter', 'destination'],
            'ids' => ['shelter-1'],
            'lat' => '30.1',
            'lng' => '57.2',
            'radius' => '5',
            'focus' => 'shelter-1',
        ]);

        parse_str((string) parse_url($url, PHP_URL_QUERY), $query);

        $this->assertSame([
            'types' => 'shelter,destination',
            'ids' => 'shelter-1',
            'lat' => '30.1',
            'lng' => '57.2',
            'r' => '5',
            'focus' => 'shelter-1',
        ], $query);
    }

    public function test_radius_and_zoom_are_ignored_without_a_point(): void
    {
        $this->assertSame(route('map'), MapLink::url(['radius' => 5, 'zoom' => 12]));
    }

    public function test_admin_can_generate_link_and_qr(): void
    {
        $this->seed(PlatformDemoSeeder::class);
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)
            ->get(route('panel.admin.map-links', ['types' => ['shelter'], 'lat' => 30.2, 'lng' => 57.1, 'radius' => 3]))
            ->assertOk()
            ->assertSee('<svg', false)
            ->assertSee('types=shelter', false);

        $link = ShortLink::query()->firstOrFail();
        $this->assertStringContainsString('types=shelter', $link->query);
    }

    public function test_visitor_can_share_the_current_map_state(): void
    {
        $response = $this->postJson(route('map.share'), ['query' => 'types=shelter&lat=30.2&lng=57.1&z=12&evil=<script>&ids=x'])
            ->assertOk();

        $link = ShortLink::query()->firstOrFail();
        $this->assertSame('types=shelter&lat=30.2&lng=57.1&z=12', $link->query);
        $response->assertJsonPath('url', route('map.short', $link->code));

        $this->postJson(route('map.share'), ['query' => 'z=12&lat=30.2&lng=57.1&types=shelter'])->assertOk();
        $this->assertSame(1, ShortLink::query()->count());

        $this->get(route('map.short', $link->code))->assertRedirect(route('map').'?'.$link->query);
        $this->assertSame(1, $link->fresh()->hits);

        $this->get(route('map.short.qr', $link->code))
            ->assertOk()
            ->assertHeader('Content-Type', 'image/svg+xml');

        $this->get(route('map.short', 'nope00'))->assertNotFound();
    }

    public function test_invalid_options_are_rejected_and_guests_are_blocked(): void
    {
        $this->seed(PlatformDemoSeeder::class);
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)
            ->get(route('panel.admin.map-links', ['types' => ['nope']]))
            ->assertSessionHasErrors('types.0');

        auth()->logout();
        $this->get(route('panel.admin.map-links'))->assertRedirect();
    }
}
