<?php

namespace Database\Factories;

use App\Enums\ProjectCategory;
use App\Enums\ProjectStatus;
use App\Models\SupportProject;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SupportProject>
 */
class SupportProjectFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => 'طرح '.fake()->unique()->numerify('حمایتی ###'),
            'slug' => 'project-'.fake()->unique()->numerify('####'),
            'category' => ProjectCategory::CoachTraining,
            'summary' => fake()->sentence(10),
            'description' => fake()->paragraph(),
            'budget' => 100_000_000,
            'status' => ProjectStatus::Active,
            'is_featured' => false,
            'sort_order' => 0,
        ];
    }
}
