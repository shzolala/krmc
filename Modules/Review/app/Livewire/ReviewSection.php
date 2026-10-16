<?php

namespace Modules\Review\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Modules\Review\Actions\SubmitReviewAction;
use Modules\Review\Models\Review;
use Modules\Review\Support\Reviewables;

class ReviewSection extends Component
{
    public Model $reviewable;

    public int $rating = 5;

    public string $title = '';

    public string $body = '';

    public bool $submitted = false;

    public function mount(Model $reviewable): void
    {
        Reviewables::ensureSupported($reviewable);

        $this->reviewable = $reviewable;
    }

    public function submit(SubmitReviewAction $action): void
    {
        $user = Auth::user();

        abort_unless($user !== null, 403);
        $this->authorize('create', Review::class);

        $validated = $this->validate([
            'rating' => ['required', 'integer', 'min:'.config('review.min_rating', 1), 'max:'.config('review.max_rating', 5)],
            'title' => ['nullable', 'string', 'max:'.config('review.title_max', 120)],
            'body' => ['required', 'string', 'min:10', 'max:'.config('review.body_max', 2000)],
        ]);

        $action->handle($user, $this->reviewable, [
            'rating' => $validated['rating'],
            'title' => $validated['title'] ?: null,
            'body' => $validated['body'],
        ]);

        $this->reset('title', 'body');
        $this->rating = 5;
        $this->submitted = true;
    }

    public function render(): View
    {
        $approvedReviews = $this->reviewable
            ->reviews()
            ->approved()
            ->with('user:id,name')
            ->latest()
            ->get();

        $averageRating = $approvedReviews->avg('rating');
        $userReview = Auth::check()
            ? $this->reviewable->reviews()->where('user_id', Auth::id())->first()
            : null;

        return view('review::livewire.review-section', [
            'approvedReviews' => $approvedReviews,
            'averageRating' => $averageRating === null ? null : round((float) $averageRating, 1),
            'reviewsCount' => $approvedReviews->count(),
            'userReview' => $userReview,
        ]);
    }
}
