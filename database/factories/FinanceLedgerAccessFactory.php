<?php

namespace Database\Factories;

use App\Enums\UserRole;
use App\Models\FinanceLedgerAccess;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FinanceLedgerAccess>
 */
class FinanceLedgerAccessFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'role' => UserRole::Club,
            'user_id' => null,
            'note' => null,
        ];
    }
}
