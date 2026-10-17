<?php

namespace Database\Factories;

use App\Enums\ProgramStatus;
use App\Models\Club;
use App\Models\CoachProfile;
use App\Models\Destination;
use App\Models\Program;
use App\Support\CatalogCover;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Program>
 */
class ProgramFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = 'برنامه '.fake()->unique()->numerify('###');

        return [
            'title' => $title,
            'slug' => 'program-'.fake()->unique()->numerify('####'),
            'summary' => fake()->sentence(14),
            'description' => fake()->paragraphs(3, true),
            'status' => ProgramStatus::Upcoming,
            'destination_id' => Destination::factory(),
            'club_id' => Club::factory(),
            'leader_id' => null,
            'starts_at' => now()->addDays(fake()->numberBetween(3, 30)),
            'ends_at' => now()->addDays(fake()->numberBetween(31, 40)),
            'capacity' => fake()->numberBetween(8, 40),
            'registered_count' => fake()->numberBetween(0, 8),
            'cover_path' => CatalogCover::for('program'),
            'is_featured' => false,
            'view_count' => fake()->numberBetween(10, 500),
        ];
    }

    public function ongoing(): static
    {
        return $this->state(fn (): array => [
            'status' => ProgramStatus::Ongoing,
            'starts_at' => now()->subDays(1),
            'ends_at' => now()->addDays(2),
        ]);
    }

    public function upcoming(): static
    {
        return $this->state(fn (): array => [
            'status' => ProgramStatus::Upcoming,
            'starts_at' => now()->addDays(7),
            'ends_at' => now()->addDays(9),
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (): array => [
            'status' => ProgramStatus::Completed,
            'starts_at' => now()->subDays(14),
            'ends_at' => now()->subDays(12),
        ]);
    }

    public function withLeader(?CoachProfile $leader = null): static
    {
        return $this->state(fn (): array => [
            'leader_id' => $leader?->id ?? CoachProfile::factory(),
        ]);
    }
}
