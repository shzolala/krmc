<?php

namespace Tests\Feature;

use App\Models\Club;
use App\Models\Destination;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Modules\Review\Enums\ReviewStatus;
use Modules\Review\Livewire\ModerateReviews;
use Modules\Review\Livewire\ReviewSection;
use Modules\Review\Models\Review;
use Tests\TestCase;

class ReviewModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_club_show_page_renders_reviews_section(): void
    {
        $club = Club::factory()->create();

        $this->get(route('clubs.show', $club))
            ->assertOk()
            ->assertSee('نظرات و امتیازها', false);
    }

    public function test_authenticated_user_can_submit_pending_review(): void
    {
        $user = User::factory()->create();
        $club = Club::factory()->create();

        Livewire::actingAs($user)
            ->test(ReviewSection::class, ['reviewable' => $club])
            ->set('rating', 4)
            ->set('title', 'برنامه خوب')
            ->set('body', 'تجربه بسیار مناسبی از باشگاه داشتم.')
            ->call('submit')
            ->assertHasNoErrors()
            ->assertSet('submitted', true);

        $this->assertDatabaseHas('reviews', [
            'user_id' => $user->id,
            'reviewable_type' => 'club',
            'reviewable_id' => $club->id,
            'rating' => 4,
            'status' => ReviewStatus::Pending->value,
        ]);
    }

    public function test_pending_reviews_are_hidden_from_public_list(): void
    {
        $club = Club::factory()->create();
        $author = User::factory()->create(['name' => 'نویسنده پنهان']);

        Review::factory()
            ->for($author)
            ->forReviewable($club)
            ->pending()
            ->create([
                'body' => 'این نظر هنوز نباید دیده شود.',
            ]);

        Livewire::test(ReviewSection::class, ['reviewable' => $club])
            ->assertDontSee('این نظر هنوز نباید دیده شود.')
            ->assertDontSee('نویسنده پنهان');
    }

    public function test_approved_reviews_are_visible_on_reviewable(): void
    {
        $destination = Destination::factory()->create();
        $author = User::factory()->create(['name' => 'کوهنورد تأییدشده']);

        Review::factory()
            ->for($author)
            ->forReviewable($destination)
            ->approved()
            ->create([
                'rating' => 5,
                'body' => 'مقصد عالی برای برنامه زمستانه بود.',
            ]);

        Livewire::test(ReviewSection::class, ['reviewable' => $destination])
            ->assertSee('مقصد عالی برای برنامه زمستانه بود.', false)
            ->assertSee('کوهنورد تأییدشده', false)
            ->assertSee('5.0', false);
    }

    public function test_user_cannot_submit_more_than_one_review_per_entity(): void
    {
        $user = User::factory()->create();
        $club = Club::factory()->create();

        Review::factory()
            ->for($user)
            ->forReviewable($club)
            ->pending()
            ->create();

        Livewire::actingAs($user)
            ->test(ReviewSection::class, ['reviewable' => $club])
            ->set('rating', 3)
            ->set('body', 'تلاش دوباره برای ثبت نظر تکراری.')
            ->call('submit')
            ->assertHasErrors(['body']);
    }

    public function test_admin_can_approve_pending_review(): void
    {
        $admin = User::factory()->admin()->create();
        $club = Club::factory()->create();
        $review = Review::factory()
            ->forReviewable($club)
            ->pending()
            ->create();

        Livewire::actingAs($admin)
            ->test(ModerateReviews::class)
            ->call('approve', $review->id)
            ->assertHasNoErrors();

        $this->assertTrue($review->fresh()->isApproved());
        $this->assertSame($admin->id, $review->fresh()->moderated_by);
    }

    public function test_non_admin_cannot_access_moderation(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(ModerateReviews::class)
            ->assertForbidden();
    }
}
