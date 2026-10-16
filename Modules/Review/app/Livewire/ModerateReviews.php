<?php

namespace Modules\Review\Livewire;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Review\Actions\ApproveReviewAction;
use Modules\Review\Actions\RejectReviewAction;
use Modules\Review\Enums\ReviewStatus;
use Modules\Review\Models\Review;

class ModerateReviews extends Component
{
    use WithPagination;

    public string $filter = 'pending';

    public function mount(): void
    {
        $this->authorize('moderate', Review::class);
    }

    public function updatingFilter(): void
    {
        $this->resetPage();
    }

    public function approve(int $reviewId, ApproveReviewAction $action): void
    {
        $review = Review::query()->findOrFail($reviewId);
        $this->authorize('moderate', Review::class);

        /** @var User $moderator */
        $moderator = Auth::user();

        $action->handle($review, $moderator);
    }

    public function reject(int $reviewId, RejectReviewAction $action): void
    {
        $review = Review::query()->findOrFail($reviewId);
        $this->authorize('moderate', Review::class);

        /** @var User $moderator */
        $moderator = Auth::user();

        $action->handle($review, $moderator);
    }

    public function render(): View
    {
        $reviews = Review::query()
            ->with(['user:id,name', 'reviewable'])
            ->when(
                $this->filter !== 'all',
                fn ($query) => $query->where('status', ReviewStatus::from($this->filter)),
            )
            ->latest()
            ->paginate(15);

        return view('review::livewire.moderate-reviews', [
            'reviews' => $reviews,
        ])->layout('layouts::app', [
            'title' => 'مدیریت نظرات',
        ]);
    }
}
