<?php

namespace Modules\Review\Actions;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;
use Modules\Review\Enums\ReviewStatus;
use Modules\Review\Models\Review;
use Modules\Review\Support\Reviewables;

class SubmitReviewAction
{
    /**
     * @param  array{rating: int, body: string, title?: string|null}  $data
     */
    public function handle(User $user, Model $reviewable, array $data): Review
    {
        Reviewables::ensureSupported($reviewable);

        $exists = Review::query()
            ->where('user_id', $user->id)
            ->where('reviewable_type', $reviewable->getMorphClass())
            ->where('reviewable_id', $reviewable->getKey())
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'body' => 'شما قبلاً برای این مورد نظر ثبت کرده‌اید.',
            ]);
        }

        return Review::query()->create([
            'user_id' => $user->id,
            'reviewable_type' => $reviewable->getMorphClass(),
            'reviewable_id' => $reviewable->getKey(),
            'rating' => $data['rating'],
            'title' => $data['title'] ?? null,
            'body' => $data['body'],
            'status' => ReviewStatus::Pending,
        ]);
    }
}
