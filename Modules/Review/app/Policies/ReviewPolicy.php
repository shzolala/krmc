<?php

namespace Modules\Review\Policies;

use App\Models\User;
use Modules\Review\Models\Review;

class ReviewPolicy
{
    public function create(User $user): bool
    {
        return true;
    }

    public function moderate(User $user): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Review $review): bool
    {
        return $user->isAdmin() || $review->user_id === $user->id;
    }
}
