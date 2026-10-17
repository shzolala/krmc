<?php

namespace Modules\Review\Livewire;

use App\Models\User;
use App\Support\Admin\AdminResources;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Review\Actions\ApproveReviewAction;
use Modules\Review\Actions\RejectReviewAction;
use Modules\Review\Enums\ReviewStatus;
use Modules\Review\Models\Review;

class ModerateReviews extends Component
{
    use WithPagination;

    #[Url(as: 'status')]
    public string $filter = 'pending';

    #[Url(as: 'q')]
    public string $search = '';

    #[Url]
    public string $rating = '';

    public function mount(): void
    {
        $this->authorize('moderate', Review::class);
    }

    public function updatingFilter(): void
    {
        $this->resetPage();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingRating(): void
    {
        $this->resetPage();
    }

    public function approve(int $reviewId, ApproveReviewAction $action): void
    {
        $this->authorize('moderate', Review::class);

        $action->handle(Review::query()->findOrFail($reviewId), $this->moderator());
    }

    public function reject(int $reviewId, RejectReviewAction $action): void
    {
        $this->authorize('moderate', Review::class);

        $action->handle(Review::query()->findOrFail($reviewId), $this->moderator());
    }

    public function reopen(int $reviewId): void
    {
        $this->authorize('moderate', Review::class);

        Review::query()->findOrFail($reviewId)->update([
            'status' => ReviewStatus::Pending,
            'moderated_by' => null,
            'moderated_at' => null,
        ]);
    }

    public function delete(int $reviewId): void
    {
        $review = Review::query()->findOrFail($reviewId);
        $this->authorize('delete', $review);

        $review->delete();
    }

    /**
     * @return array{name: string, type: string, admin_url: ?string, public_url: ?string}
     */
    public function describe(Review $review): array
    {
        $reviewable = $review->reviewable;

        if (! $reviewable instanceof Model) {
            return ['name' => 'مورد حذف‌شده', 'type' => '—', 'admin_url' => null, 'public_url' => null];
        }

        $resource = collect(AdminResources::all())->first(
            fn ($candidate): bool => $candidate->model() === $reviewable::class,
        );

        return [
            'name' => $resource ? $resource->recordTitle($reviewable) : (string) ($reviewable->name ?? $reviewable->title ?? '#'.$reviewable->getKey()),
            'type' => $resource?->singular() ?? class_basename($reviewable),
            'admin_url' => $resource ? route('panel.admin.resources.edit', ['resource' => $resource->key(), 'id' => $reviewable->getKey()]) : null,
            'public_url' => $resource?->publicUrl($reviewable),
        ];
    }

    private function moderator(): User
    {
        /** @var User $moderator */
        $moderator = Auth::user();

        return $moderator;
    }

    public function render(): View
    {
        $search = trim($this->search);

        $reviews = Review::query()
            ->with(['user:id,name', 'reviewable', 'moderator:id,name'])
            ->when(
                $this->filter !== 'all' && ReviewStatus::tryFrom($this->filter) !== null,
                fn ($query) => $query->where('status', ReviewStatus::from($this->filter)),
            )
            ->when($this->rating !== '', fn ($query) => $query->where('rating', (int) $this->rating))
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($where) use ($search): void {
                    $where->where('title', 'like', '%'.$search.'%')
                        ->orWhere('body', 'like', '%'.$search.'%')
                        ->orWhereHas('user', fn ($user) => $user->where('name', 'like', '%'.$search.'%'));
                });
            })
            ->latest()
            ->paginate(15);

        $counts = Review::query()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('review::livewire.moderate-reviews', [
            'reviews' => $reviews,
            'counts' => $counts,
            'statuses' => ReviewStatus::cases(),
        ])->layout('layouts::admin', [
            'title' => 'مدیریت نظرات',
        ]);
    }
}
