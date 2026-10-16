<?php

namespace Database\Factories;

use App\Enums\LodgingType;
use App\Models\Accommodation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Accommodation>
 */
class AccommodationFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'اقامتگاه '.fake()->unique()->numerify('###'),
            'slug' => 'accommodation-'.fake()->unique()->numerify('####'),
            'type' => fake()->randomElement(LodgingType::cases()),
            'summary' => fake()->sentence(14),
            'description' => fake()->paragraphs(2, true),
            'city' => 'کرمان',
            'cover_path' => 'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?auto=format&fit=crop&w=1200&q=80',
            'latitude' => fake()->latitude(29.2, 30.5),
            'longitude' => fake()->longitude(56.8, 57.6),
            'address' => 'کرمان',
            'is_verified' => true,
            'is_featured' => fake()->boolean(40),
            'is_premium' => fake()->boolean(20),
        ];
    }
}
