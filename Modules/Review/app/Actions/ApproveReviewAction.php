<?php

namespace Modules\Review\Actions;

use App\Models\User;
use Modules\Review\Enums\ReviewStatus;
use Modules\Review\Models\Review;

class ApproveReviewAction
{
    public function handle(Review $review, User $moderator): Review
    {
        $review->update([
            'status' => ReviewStatus::Approved,
            'moderated_by' => $moderator->id,
            'moderated_at' => now(),
        ]);

        return $review->refresh();
    }
}
