<?php

namespace Database\Factories;

use App\Models\CoachProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<CoachProfile>
 */
class CoachProfileFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $slugBase = Str::slug(fake()->unique()->userName());

        return [
            'user_id' => User::factory(),
            'slug' => $slugBase,
            'headline' => fake()->sentence(8),
            'bio' => fake()->paragraph(),
            'specialty' => fake()->randomElement(['کوهنوردی فنی', 'یخ‌نوردی', 'صخره‌نوردی', 'طبیعت‌گردی']),
            'experience_years' => fake()->numberBetween(3, 25),
            'is_verified' => true,
            'is_featured' => fake()->boolean(40),
            'is_premium' => fake()->boolean(20),
            'is_leader' => fake()->boolean(40),
        ];
    }
}
