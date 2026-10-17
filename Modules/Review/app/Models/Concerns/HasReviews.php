<?php

namespace Modules\Review\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Modules\Review\Models\Review;

trait HasReviews
{
    public function reviews(): MorphMany
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    public function approvedReviews(): MorphMany
    {
        return $this->reviews()->approved()->latest();
    }

    public function averageApprovedRating(): ?float
    {
        $average = $this->approvedReviews()->avg('rating');

        return $average === null ? null : round((float) $average, 1);
    }

    public function approvedReviewsCount(): int
    {
        return $this->approvedReviews()->count();
    }

    /**
     * @param  Builder<static>  $query
     * @return Builder<static>
     */
    public function scopeWithReviewStats(Builder $query): Builder
    {
        $approved = fn ($reviews) => $reviews->approved();

        return $query
            ->withCount(['reviews as approved_reviews_count' => $approved])
            ->withAvg(['reviews as approved_reviews_avg_rating' => $approved], 'rating');
    }

    /**
     * @return array{average: float|null, count: int}
     */
    public function reviewSummary(): array
    {
        if (array_key_exists('approved_reviews_count', $this->attributes)) {
            $average = $this->attributes['approved_reviews_avg_rating'] ?? null;

            return [
                'average' => $average === null ? null : round((float) $average, 1),
                'count' => (int) $this->attributes['approved_reviews_count'],
            ];
        }

        return [
            'average' => $this->averageApprovedRating(),
            'count' => $this->approvedReviewsCount(),
        ];
    }
}
