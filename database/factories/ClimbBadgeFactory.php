<?php

namespace Database\Factories;

use App\Enums\PlanBadgeColor;
use App\Models\ClimbBadge;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ClimbBadge>
 */
class ClimbBadgeFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'نشان '.fake()->unique()->numerify('###'),
            'description' => fake()->sentence(6),
            'required_count' => 1,
            'min_elevation' => null,
            'color' => PlanBadgeColor::Zinc,
            'icon' => 'star',
            'sort_order' => 0,
            'is_active' => true,
        ];
    }
}
