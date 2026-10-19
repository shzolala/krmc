<?php

namespace Database\Factories;

use App\Enums\FundIncomeSource;
use App\Models\FundIncome;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FundIncome>
 */
class FundIncomeFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'source' => FundIncomeSource::Donation,
            'amount' => fake()->numberBetween(1, 50) * 100_000,
            'received_at' => now()->subDays(fake()->numberBetween(0, 90))->toDateString(),
            'payer_name' => fake()->name(),
            'note' => null,
        ];
    }
}
