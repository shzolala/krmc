<?php

namespace Database\Factories;

use App\Enums\AttractionType;
use App\Models\Attraction;
use App\Models\MountainRegion;
use App\Support\CatalogCover;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Attraction>
 */
class AttractionFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = 'جاذبه '.fake()->unique()->numerify('###');
        $catalogId = 'att-'.Str::lower(Str::random(8));
        $type = fake()->randomElement(AttractionType::cases());

        return [
            'catalog_id' => $catalogId,
            'mountain_region_id' => MountainRegion::factory(),
            'name' => $name,
            'name_en' => fake()->words(3, true),
            'slug' => Str::slug($catalogId),
            'alt_names' => [],
            'type' => $type,
            'types' => [$type->value],
            'era' => null,
            'unesco' => ['inscribed' => false],
            'province' => 'کرمان',
            'county' => 'کرمان',
            'city' => 'کرمان',
            'latitude' => fake()->latitude(29.2, 30.5),
            'longitude' => fake()->longitude(56.8, 57.6),
            'summary' => fake()->sentence(14),
            'description' => fake()->paragraphs(2, true),
            'cover_path' => CatalogCover::for($catalogId),
            'is_featured' => fake()->boolean(30),
            'view_count' => fake()->numberBetween(40, 4000),
        ];
    }
}
