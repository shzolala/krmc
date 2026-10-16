<?php

namespace Database\Factories;

use App\Models\Club;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Club>
 */
class ClubFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = 'باشگاه '.fake()->unique()->numerify('کوهنوردی ###');

        return [
            'name' => $name,
            'slug' => 'club-'.fake()->unique()->numerify('####'),
            'summary' => fake()->sentence(14),
            'description' => fake()->paragraphs(3, true),
            'city' => 'کرمان',
            'latitude' => fake()->latitude(30.2, 30.35),
            'longitude' => fake()->longitude(57.0, 57.15),
            'address' => 'کرمان',
            'founded_year' => fake()->numberBetween(1375, 1402),
            'is_verified' => fake()->boolean(70),
            'is_featured' => fake()->boolean(40),
            'is_premium' => fake()->boolean(25),
            'member_count' => fake()->numberBetween(20, 420),
            'program_count' => fake()->numberBetween(2, 48),
            'view_count' => fake()->numberBetween(50, 5000),
        ];
    }
}
