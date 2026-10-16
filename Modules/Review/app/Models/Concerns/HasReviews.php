<?php

namespace Modules\Review\Models\Concerns;

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
}
