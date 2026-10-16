<?php

namespace Modules\Review\Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Modules\Review\Enums\ReviewStatus;
use Modules\Review\Models\Review;

/**
 * @extends Factory<Review>
 */
class ReviewFactory extends Factory
{
    protected $model = Review::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'rating' => fake()->numberBetween(1, 5),
            'title' => fake()->optional()->sentence(4),
            'body' => fake()->paragraph(),
            'status' => ReviewStatus::Pending,
        ];
    }

    public function forReviewable(Model $reviewable): static
    {
        return $this->state(fn (array $attributes): array => [
            'reviewable_type' => $reviewable->getMorphClass(),
            'reviewable_id' => $reviewable->getKey(),
        ]);
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => ReviewStatus::Approved,
            'moderated_at' => now(),
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => ReviewStatus::Rejected,
            'moderated_at' => now(),
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => ReviewStatus::Pending,
            'moderated_by' => null,
            'moderated_at' => null,
        ]);
    }
}
