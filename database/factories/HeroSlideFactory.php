<?php

namespace Database\Factories;

use App\Models\HeroSlide;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<HeroSlide>
 */
class HeroSlideFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => 'اسلاید '.fake()->unique()->numerify('###'),
            'subtitle' => fake()->sentence(8),
            'image_path' => 'images/covers/hero.jpg',
            'mountain_region_id' => null,
            'destination_id' => null,
            'program_id' => null,
            'sort_order' => 0,
            'is_active' => true,
        ];
    }
}
