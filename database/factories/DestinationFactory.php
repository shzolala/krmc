<?php

namespace Database\Factories;

use App\Models\Destination;
use Illuminate\Database\Eloquent\Factories\Factory;

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

        return [
            'name' => $name,
            'slug' => 'destination-'.fake()->unique()->numerify('####'),
            'region' => 'کرمان',
            'elevation' => fake()->numberBetween(2500, 4500),
            'summary' => fake()->sentence(16),
            'description' => fake()->paragraphs(3, true),
            'cover_path' => 'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?auto=format&fit=crop&w=1200&q=80',
            'latitude' => fake()->latitude(29.2, 30.5),
            'longitude' => fake()->longitude(56.8, 57.6),
            'address' => 'کرمان',
            'is_featured' => fake()->boolean(50),
            'view_count' => fake()->numberBetween(100, 8000),
        ];
    }
}
