<?php

namespace Database\Factories;

use App\Models\Shelter;
use App\Support\CatalogCover;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Shelter>
 */
class ShelterFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'پناهگاه '.fake()->unique()->numerify('###'),
            'slug' => 'shelter-'.fake()->unique()->numerify('####'),
            'summary' => fake()->sentence(14),
            'description' => fake()->paragraphs(2, true),
            'elevation' => fake()->numberBetween(2500, 3800),
            'capacity' => fake()->numberBetween(10, 40),
            'cover_path' => CatalogCover::path('cover-17.jpg'),
            'latitude' => fake()->latitude(29.2, 30.5),
            'longitude' => fake()->longitude(56.8, 57.6),
            'address' => 'مسیر کوهستانی',
            'is_featured' => true,
        ];
    }
}
