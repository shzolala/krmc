<?php

namespace Database\Factories;

use App\Models\SponsorshipPackage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SponsorshipPackage>
 */
class SponsorshipPackageFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'بسته '.fake()->unique()->numerify('###'),
            'min_amount' => fake()->randomElement([10_000_000, 30_000_000, 80_000_000]),
            'description' => fake()->sentence(10),
            'benefits' => ['نمایش لوگو در صفحه اصلی', 'صفحه معرفی اختصاصی'],
            'sort_order' => 0,
            'is_active' => true,
        ];
    }
}
