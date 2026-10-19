<?php

namespace Database\Factories;

use App\Models\Destination;
use App\Models\PeakAscent;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PeakAscent>
 */
class PeakAscentFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'destination_id' => Destination::factory(),
            'climbed_on' => now()->subDays(fake()->numberBetween(1, 300))->toDateString(),
            'note' => null,
        ];
    }
}
