<?php

namespace Database\Factories;

use App\Models\ProgramReport;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProgramReport>
 */
class ProgramReportFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = 'گزارش برنامه '.fake()->unique()->numerify('###');

        return [
            'title' => $title,
            'slug' => 'report-'.fake()->unique()->numerify('####'),
            'excerpt' => fake()->sentence(18),
            'body' => fake()->paragraphs(4, true),
            'cover_path' => 'https://images.unsplash.com/photo-1486870591958-9b9d0d1dda99?auto=format&fit=crop&w=1200&q=80',
            'published_at' => now()->subDays(fake()->numberBetween(1, 90)),
            'view_count' => fake()->numberBetween(20, 2000),
        ];
    }
}
