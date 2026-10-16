<?php

namespace Modules\Review\Actions;

use App\Models\User;
use Modules\Review\Enums\ReviewStatus;
use Modules\Review\Models\Review;

class RejectReviewAction
{
    public function handle(Review $review, User $moderator): Review
    {
        $review->update([
            'status' => ReviewStatus::Rejected,
            'moderated_by' => $moderator->id,
            'moderated_at' => now(),
        ]);

        return $review->refresh();
    }
}
