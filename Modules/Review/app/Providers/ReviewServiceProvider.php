<?php

namespace Modules\Review\Providers;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Gate;
use Livewire\Livewire;
use Modules\Review\Livewire\ModerateReviews;
use Modules\Review\Livewire\ReviewSection;
use Modules\Review\Models\Review;
use Modules\Review\Policies\ReviewPolicy;
use Modules\Review\Support\Reviewables;
use Nwidart\Modules\Support\ModuleServiceProvider;

class ReviewServiceProvider extends ModuleServiceProvider
{
    protected string $name = 'Review';

    protected string $nameLower = 'review';

    /**
     * @var string[]
     */
    protected array $providers = [
        EventServiceProvider::class,
        RouteServiceProvider::class,
    ];

    public function boot(): void
    {
        parent::boot();

        Relation::morphMap(Reviewables::map());

        Gate::policy(Review::class, ReviewPolicy::class);

        Livewire::component('review.section', ReviewSection::class);
        Livewire::component('review.moderate', ModerateReviews::class);
    }
}
