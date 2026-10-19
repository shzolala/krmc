<?php

namespace Database\Factories;

use App\Models\MembershipDue;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MembershipDue>
 */
class MembershipDueFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => 'حق عضویت '.fake()->unique()->numerify('####'),
            'period' => fake()->unique()->numerify('####'),
            'amount' => fake()->randomElement([1_000_000, 2_000_000, 5_000_000]),
            'due_date' => now()->addMonths(3)->toDateString(),
            'note' => null,
        ];
    }
}
