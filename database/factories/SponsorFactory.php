<?php

namespace Database\Factories;

use App\Enums\SponsorStatus;
use App\Models\Sponsor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Sponsor>
 */
class SponsorFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'شرکت '.fake()->unique()->numerify('نمونه ###'),
            'slug' => 'sponsor-'.fake()->unique()->numerify('####'),
            'logo_path' => null,
            'website' => null,
            'summary' => fake()->sentence(12),
            'description' => fake()->paragraph(),
            'support_note' => fake()->sentence(10),
            'pledge_amount' => 0,
            'show_amount' => true,
            'status' => SponsorStatus::Active,
            'is_featured' => false,
            'sort_order' => 0,
        ];
    }

    public function pending(): static
    {
        return $this->state(['status' => SponsorStatus::Pending]);
    }
}
