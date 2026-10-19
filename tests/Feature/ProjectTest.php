<?php

namespace Tests\Feature;

use App\Enums\FundIncomeSource;
use App\Enums\ProjectCategory;
use App\Enums\ProjectStatus;
use App\Livewire\Panel\EditSponsorProfile;
use App\Models\FundExpense;
use App\Models\FundIncome;
use App\Models\ProjectActivity;
use App\Models\Sponsor;
use App\Models\SponsorshipPackage;
use App\Models\SupportProject;
use App\Models\User;
use App\Support\FundReport;
use Database\Seeders\ProjectSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ProjectTest extends TestCase
{
    use RefreshDatabase;

    private function contribute(SupportProject $project, Sponsor $sponsor, int $amount, array $attributes = []): FundIncome
    {
        return FundIncome::factory()->create([
            'source' => FundIncomeSource::Sponsorship,
            'amount' => $amount,
            'sponsor_id' => $sponsor->id,
            'support_project_id' => $project->id,
            'payer_name' => null,
            ...$attributes,
        ]);
    }

    public function test_project_funding_combines_cash_and_in_kind_and_tracks_spending(): void
    {
        $project = SupportProject::factory()->create(['budget' => 100_000_000]);
        $sponsor = Sponsor::factory()->create();
        $this->contribute($project, $sponsor, 30_000_000);
        $this->contribute($project, $sponsor, 20_000_000, ['is_in_kind' => true, 'in_kind_description' => 'طناب', 'installment_no' => 2]);
        FundExpense::factory()->create(['support_project_id' => $project->id, 'amount' => 10_000_000]);

        $funded = SupportProject::query()->withFunding()->findOrFail($project->id);

        $this->assertSame(30_000_000, $funded->cashRaised());
        $this->assertSame(20_000_000, $funded->inKindRaised());
        $this->assertSame(50_000_000, $funded->raisedTotal());
        $this->assertSame(10_000_000, $funded->spentTotal());
        $this->assertSame(50, $funded->progressPercent());
    }

    public function test_in_kind_support_is_not_counted_in_the_cash_balance(): void
    {
        $project = SupportProject::factory()->create();
        $sponsor = Sponsor::factory()->create();
        $this->contribute($project, $sponsor, 10_000_000);
        $this->contribute($project, $sponsor, 4_000_000, ['is_in_kind' => true, 'in_kind_description' => 'کالا']);

        $summary = FundReport::summary();

        $this->assertSame(10_000_000, $summary['income']);
        $this->assertSame(4_000_000, $summary['inKind']);
        $this->assertSame(10_000_000, $summary['balance']);
    }

    public function test_projects_page_filters_by_category_and_shows_progress(): void
    {
        SupportProject::factory()->create(['title' => 'طرح مربی تست', 'category' => ProjectCategory::CoachTraining, 'budget' => 100_000_000]);
        SupportProject::factory()->create(['title' => 'طرح پناهگاه تست', 'category' => ProjectCategory::ShelterPreservation]);

        $this->get(route('projects.index'))
            ->assertOk()
            ->assertSee('طرح مربی تست', false)
            ->assertSee('طرح پناهگاه تست', false);

        $this->get(route('projects.index', ['category' => 'shelter_preservation']))
            ->assertSee('طرح پناهگاه تست', false)
            ->assertDontSee('طرح مربی تست', false);
    }

    public function test_project_page_shows_activities_sponsors_rounds_and_finances(): void
    {
        $project = SupportProject::factory()->create(['title' => 'طرح کامل تست', 'budget' => 80_000_000]);
        ProjectActivity::factory()->create(['support_project_id' => $project->id, 'title' => 'کارگاه آموزشی تست']);
        $sponsor = Sponsor::factory()->create(['name' => 'حامی طرح تست']);
        $this->contribute($project, $sponsor, 20_000_000, ['installment_no' => 1]);
        $this->contribute($project, $sponsor, 12_000_000, ['installment_no' => 2, 'is_in_kind' => true, 'in_kind_description' => 'کیف نجات']);

        $this->get(route('projects.show', $project))
            ->assertOk()
            ->assertSee('کارگاه آموزشی تست', false)
            ->assertSee('حامی طرح تست', false)
            ->assertSee('نوبت ۱', false)
            ->assertSee('نوبت ۲', false)
            ->assertSee('کیف نجات', false)
            ->assertSee(fa_num(32_000_000), false)
            ->assertSee('حمایت غیرنقدی', false);
    }

    public function test_project_page_hides_amounts_of_sponsors_who_opted_out_and_pending_sponsors(): void
    {
        $project = SupportProject::factory()->create();
        $private = Sponsor::factory()->create(['name' => 'حامی پنهان‌مبلغ', 'show_amount' => false]);
        $pending = Sponsor::factory()->pending()->create(['name' => 'حامی نامعتبر']);
        $this->contribute($project, $private, 7_777_000);
        $this->contribute($project, $pending, 1_000_000);

        $this->get(route('projects.show', $project))
            ->assertSee('حامی پنهان‌مبلغ', false)
            ->assertDontSee(fa_num(7_777_000).' تومان', false)
            ->assertDontSee('حامی نامعتبر', false);
    }

    public function test_sponsor_page_lists_supported_projects_with_rounds(): void
    {
        $project = SupportProject::factory()->create(['title' => 'طرح حمایت‌شده']);
        $sponsor = Sponsor::factory()->create();
        $this->contribute($project, $sponsor, 5_000_000, ['installment_no' => 3, 'is_in_kind' => true, 'in_kind_description' => 'تجهیزات اردو']);

        $this->get(route('sponsors.show', $sponsor))
            ->assertOk()
            ->assertSee('طرح حمایت‌شده', false)
            ->assertSee('نوبت ۳', false)
            ->assertSee('تجهیزات اردو', false);
    }

    public function test_admin_records_in_kind_support_with_round_and_requires_a_description(): void
    {
        $admin = User::factory()->admin()->create();
        $project = SupportProject::factory()->create();
        $sponsor = Sponsor::factory()->create();

        $payload = ['source' => 'sponsorship', 'amount' => 9000000, 'received_at' => '2026-10-01', 'sponsor_id' => $sponsor->id, 'support_project_id' => $project->id, 'installment_no' => 2, 'is_in_kind' => '1'];

        $this->actingAs($admin)->post(route('panel.admin.resources.store', 'incomes'), $payload)
            ->assertSessionHasErrors('in_kind_description');

        $this->actingAs($admin)->post(route('panel.admin.resources.store', 'incomes'), [...$payload, 'in_kind_description' => 'طناب ۳۰۰ متری'])
            ->assertRedirect(route('panel.admin.resources.index', 'incomes'));

        $income = FundIncome::query()->firstOrFail();
        $this->assertTrue($income->is_in_kind);
        $this->assertSame(2, $income->installment_no);
    }

    public function test_sponsor_chooses_purpose_in_the_panel(): void
    {
        $user = User::factory()->shop()->create();
        $project = SupportProject::factory()->create();
        SponsorshipPackage::factory()->create(['min_amount' => 1_000_000]);

        Livewire::actingAs($user)
            ->test(EditSponsorProfile::class)
            ->set('name', 'حامی هدفمند')
            ->set('summary', 'معرفی')
            ->set('preferredCategory', 'environment')
            ->set('projectId', $project->id)
            ->set('pledgeAmount', 2_000_000)
            ->call('save')
            ->assertHasNoErrors();

        $sponsor = Sponsor::query()->firstOrFail();
        $this->assertSame(ProjectCategory::Environment, $sponsor->preferred_category);
        $this->assertSame($project->id, $sponsor->support_project_id);
    }

    public function test_seeder_builds_projects_with_funding_in_every_category(): void
    {
        $this->seed(ProjectSeeder::class);
        $this->seed(ProjectSeeder::class);

        $this->assertSame(5, SupportProject::query()->count());
        foreach (ProjectCategory::cases() as $category) {
            $this->assertTrue(SupportProject::query()->where('category', $category->value)->exists(), $category->value);
        }
        $this->assertTrue(FundIncome::query()->where('is_in_kind', true)->whereNotNull('installment_no')->exists());

        $this->get(route('projects.index'))->assertOk()->assertSee('مربی‌پروری کرمان', false);
        $this->get(route('home'))->assertOk()->assertSee('تربیت مربی', false);
    }

    public function test_home_page_shows_latest_fundraising_projects_and_no_ad_caption(): void
    {
        Sponsor::factory()->create();
        SupportProject::factory()->create(['title' => 'طرح تکمیل‌شده قدیمی', 'status' => ProjectStatus::Completed]);
        SupportProject::factory()->create(['title' => 'طرح در حال جذب', 'status' => ProjectStatus::Active]);

        $html = $this->get(route('home'))->assertOk()->getContent();

        $this->assertStringContainsString('طرح در حال جذب', $html);
        $this->assertStringContainsString('همه طرح‌ها', $html);
        $this->assertStringContainsString('snap-x', $html);
        $this->assertStringNotContainsString('نمایش لوگو بخشی از مزایای تبلیغاتی', $html);
        $this->assertLessThan(strpos($html, 'طرح تکمیل‌شده قدیمی'), strpos($html, 'طرح در حال جذب'));
    }
}
