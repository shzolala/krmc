<?php

namespace Tests\Feature;

use App\Models\Accommodation;
use App\Models\Club;
use App\Models\CoachProfile;
use App\Models\Destination;
use App\Models\Program;
use App\Models\Shelter;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ListingFiltersTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @param  array<string, mixed>  $query
     * @return list<string>
     */
    private function names(string $route, string $variable, array $query, string $column = 'name'): array
    {
        return $this->get(route($route, $query))->assertOk()->viewData($variable)->pluck($column)->all();
    }

    public function test_directory_pages_use_the_plain_header_and_floating_filter_without_hero_banners(): void
    {
        foreach (['programs', 'destinations', 'clubs', 'coaches', 'regions', 'attractions', 'accommodations', 'shelters', 'shops'] as $section) {
            $this->get(route($section.'.index'))
                ->assertOk()
                ->assertSee('data-filter-sheet', false)
                ->assertSee('grid-cols-3', false)
                ->assertDontSee('hero-overlay', false);
        }
    }

    public function test_clubs_filter_by_city_verification_and_active_programs(): void
    {
        $withProgram = Club::factory()->create(['name' => 'باشگاه برنامه‌دار', 'city' => 'کرمان', 'is_verified' => true]);
        Club::factory()->create(['name' => 'باشگاه سیرجان', 'city' => 'سیرجان', 'is_verified' => false]);
        Program::factory()->upcoming()->create(['club_id' => $withProgram->id]);

        $this->assertSame(['باشگاه سیرجان'], $this->names('clubs.index', 'clubs', ['city' => 'سیرجان']));
        $this->assertContains('باشگاه برنامه‌دار', $this->names('clubs.index', 'clubs', ['verified' => 1]));
        $this->assertNotContains('باشگاه سیرجان', $this->names('clubs.index', 'clubs', ['verified' => 1]));
        $this->assertSame(['باشگاه برنامه‌دار'], $this->names('clubs.index', 'clubs', ['has_programs' => 1]));
    }

    public function test_coaches_filter_by_club_and_verification(): void
    {
        $club = Club::factory()->create(['slug' => 'coach-club']);
        $verified = CoachProfile::factory()->create(['user_id' => User::factory()->create()->id, 'club_id' => $club->id, 'is_verified' => true]);
        CoachProfile::factory()->create(['user_id' => User::factory()->create()->id, 'is_verified' => false]);

        $this->assertSame([$verified->slug], $this->names('coaches.index', 'coaches', ['club' => 'coach-club'], 'slug'));
        $this->assertSame([$verified->slug], $this->names('coaches.index', 'coaches', ['verified' => 1], 'slug'));
    }

    public function test_accommodations_shops_and_shelters_filter_by_their_own_fields(): void
    {
        Accommodation::factory()->create(['name' => 'اقامتگاه راین', 'city' => 'راین', 'is_verified' => true]);
        Accommodation::factory()->create(['name' => 'اقامتگاه بم', 'city' => 'بم', 'is_verified' => false]);
        Shop::factory()->create(['name' => 'فروشگاه بم', 'city' => 'بم']);
        Shop::factory()->create(['name' => 'فروشگاه کرمان', 'city' => 'کرمان']);
        $destination = Destination::factory()->create(['slug' => 'peak-with-shelter']);
        Shelter::factory()->create(['name' => 'پناهگاه قله', 'destination_id' => $destination->id]);
        Shelter::factory()->create(['name' => 'پناهگاه دیگر']);

        $this->assertSame(['اقامتگاه راین'], $this->names('accommodations.index', 'accommodations', ['city' => 'راین']));
        $this->assertSame(['اقامتگاه راین'], $this->names('accommodations.index', 'accommodations', ['verified' => 1]));
        $this->assertSame(['فروشگاه بم'], $this->names('shops.index', 'shops', ['city' => 'بم']));
        $this->assertSame(['پناهگاه قله'], $this->names('shelters.index', 'shelters', ['destination' => 'peak-with-shelter']));
    }

    public function test_active_filters_are_shown_as_removable_chips(): void
    {
        $html = $this->get(route('clubs.index', ['verified' => 1, 'q' => 'اماوند']))->assertOk()->getContent();

        $this->assertStringContainsString('حذف فیلتر «اماوند»', $html);
        $this->assertStringContainsString('حذف فیلتر فقط تأییدشده فدراسیون', $html);
        $this->assertStringContainsString('پاک کردن همه', $html);
    }
}
