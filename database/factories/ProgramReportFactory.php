<?php

namespace Database\Factories;

use App\Models\ProgramReport;
use App\Support\CatalogCover;
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
            'cover_path' => CatalogCover::for('report'),
            'published_at' => now()->subDays(fake()->numberBetween(1, 90)),
            'view_count' => fake()->numberBetween(20, 2000),
        ];
    }
}
