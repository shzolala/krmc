<?php

namespace Database\Factories;

use App\Models\Shop;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Shop>
 */
class ShopFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = 'فروشگاه '.fake()->unique()->numerify('تجهیزات ###');

        return [
            'name' => $name,
            'slug' => 'shop-'.fake()->unique()->numerify('####'),
            'summary' => fake()->sentence(12),
            'city' => 'کرمان',
            'latitude' => fake()->latitude(30.25, 30.32),
            'longitude' => fake()->longitude(57.05, 57.12),
            'address' => 'کرمان',
            'is_verified' => fake()->boolean(60),
            'is_featured' => fake()->boolean(30),
            'is_premium' => fake()->boolean(20),
        ];
    }
}
