<?php

namespace Database\Factories;

use App\Models\Club;
use App\Models\CoachProfile;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->name();

        return [
            'name' => $name,
            'mobile' => '09'.fake()->unique()->numerify('#########'),
            'username' => 'user_'.fake()->unique()->numerify('######'),
            'email' => null,
            'is_admin' => false,
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function admin(): static
    {
        return $this->state(fn (array $attributes): array => [
            'is_admin' => true,
        ]);
    }

    /**
     * The account owns a club, which gives it the club role.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function club(array $attributes = []): static
    {
        return $this->afterCreating(function (User $user) use ($attributes): void {
            Club::factory()->create([...$attributes, 'owner_id' => $user->id]);
        });
    }

    /**
     * The account owns a shop, which gives it the shop role.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function shop(array $attributes = []): static
    {
        return $this->afterCreating(function (User $user) use ($attributes): void {
            Shop::factory()->create([...$attributes, 'user_id' => $user->id]);
        });
    }

    /**
     * The account has a coach profile, which gives it the coach role.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function coach(array $attributes = []): static
    {
        return $this->afterCreating(function (User $user) use ($attributes): void {
            CoachProfile::factory()->create([...$attributes, 'user_id' => $user->id, 'is_leader' => false]);
        });
    }

    /**
     * The account has a leader profile, which gives it the leader role.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function leader(array $attributes = []): static
    {
        return $this->afterCreating(function (User $user) use ($attributes): void {
            CoachProfile::factory()->create([...$attributes, 'user_id' => $user->id, 'is_leader' => true]);
        });
    }
}
