<?php

namespace Database\Factories;

use App\Enums\PlanBadgeColor;
use App\Models\MembershipPlan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MembershipPlan>
 */
class MembershipPlanFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'پلن '.fake()->unique()->numerify('###'),
            'role' => null,
            'level' => 1,
            'price' => fake()->randomElement([1_000_000, 3_000_000, 6_000_000]),
            'duration_days' => 365,
            'allow_monthly' => false,
            'allow_semiannual' => false,
            'allow_annual' => true,
            'discount_semiannual' => 0,
            'discount_annual' => 0,
            'description' => fake()->sentence(8),
            'features' => ['نمایش در دایرکتوری', 'شرکت در مجمع'],
            'badge_label' => null,
            'badge_color' => PlanBadgeColor::Zinc,
            'badge_icon' => 'star',
            'is_active' => true,
        ];
    }
}
