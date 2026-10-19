<?php

namespace Database\Factories;

use App\Models\FundCampaign;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FundCampaign>
 */
class FundCampaignFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => 'کمپین '.fake()->unique()->numerify('مربی‌پروری ###'),
            'description' => fake()->sentence(12),
            'goal_amount' => fake()->randomElement([50_000_000, 100_000_000, 200_000_000]),
            'ends_at' => now()->addMonths(6)->toDateString(),
            'is_active' => true,
        ];
    }
}
