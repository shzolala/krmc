<?php

namespace Database\Factories;

use App\Enums\MountainRegionType;
use App\Models\MountainRegion;
use App\Support\CatalogCover;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<MountainRegion>
 */
class MountainRegionFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = 'منطقه '.fake()->unique()->numerify('###');
        $catalogId = 'reg-'.Str::lower(Str::random(8));

        return [
            'catalog_id' => $catalogId,
            'name' => $name,
            'name_en' => fake()->words(2, true),
            'slug' => Str::slug($catalogId),
            'alt_names' => [],
            'type' => fake()->randomElement(MountainRegionType::cases()),
            'province' => 'کرمان',
            'counties' => ['کرمان'],
            'nearest_city' => 'کرمان',
            'latitude' => fake()->latitude(29.2, 30.5),
            'longitude' => fake()->longitude(56.8, 57.6),
            'bbox' => [
                'min_lat' => 29.0,
                'min_lng' => 56.5,
                'max_lat' => 30.8,
                'max_lng' => 58.0,
                'accuracy' => 'approximate',
            ],
            'highest_elevation' => fake()->numberBetween(2500, 4500),
            'summary' => fake()->sentence(14),
            'description' => fake()->paragraphs(2, true),
            'cover_path' => CatalogCover::for($catalogId),
            'is_featured' => fake()->boolean(40),
            'view_count' => fake()->numberBetween(50, 5000),
        ];
    }
}
