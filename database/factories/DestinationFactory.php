<?php

namespace Database\Factories;

use App\Enums\DestinationCategory;
use App\Models\Destination;
use App\Models\MountainRegion;
use App\Support\CatalogCover;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Destination>
 */
class DestinationFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = 'قله '.fake()->unique()->numerify('###');
        $catalogId = 'peak-'.Str::lower(Str::random(8));

        return [
            'catalog_id' => $catalogId,
            'mountain_region_id' => MountainRegion::factory(),
            'name' => $name,
            'name_en' => fake()->words(2, true),
            'slug' => 'destination-'.fake()->unique()->numerify('####'),
            'category' => DestinationCategory::Peak,
            'subtype' => 'summit',
            'region' => 'کرمان',
            'province' => 'کرمان',
            'elevation' => fake()->numberBetween(2500, 4500),
            'summary' => fake()->sentence(16),
            'description' => fake()->paragraphs(3, true),
            'cover_path' => CatalogCover::for($catalogId),
            'latitude' => fake()->latitude(29.2, 30.5),
            'longitude' => fake()->longitude(56.8, 57.6),
            'address' => 'کرمان',
            'is_featured' => fake()->boolean(50),
            'view_count' => fake()->numberBetween(100, 8000),
        ];
    }
}
