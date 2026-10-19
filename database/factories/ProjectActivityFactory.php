<?php

namespace Database\Factories;

use App\Models\ProjectActivity;
use App\Models\SupportProject;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProjectActivity>
 */
class ProjectActivityFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'support_project_id' => SupportProject::factory(),
            'title' => 'اقدام '.fake()->unique()->numerify('###'),
            'description' => fake()->sentence(12),
            'happened_on' => now()->subDays(fake()->numberBetween(1, 120))->toDateString(),
        ];
    }
}
