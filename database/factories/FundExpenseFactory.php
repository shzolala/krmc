<?php

namespace Database\Factories;

use App\Enums\FundExpenseCategory;
use App\Models\FundExpense;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FundExpense>
 */
class FundExpenseFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => 'هزینه '.fake()->unique()->numerify('####'),
            'category' => fake()->randomElement(FundExpenseCategory::cases()),
            'amount' => fake()->numberBetween(1, 30) * 100_000,
            'spent_at' => now()->subDays(fake()->numberBetween(0, 90))->toDateString(),
            'description' => fake()->sentence(10),
        ];
    }
}
