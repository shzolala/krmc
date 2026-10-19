<?php

namespace Tests\Feature;

use App\Enums\FundIncomeSource;
use App\Enums\SponsorStatus;
use App\Enums\UserRole;
use App\Livewire\Panel\EditSponsorProfile;
use App\Models\FundIncome;
use App\Models\Sponsor;
use App\Models\SponsorshipPackage;
use App\Models\User;
use Database\Seeders\SponsorSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SponsorTest extends TestCase
{
    use RefreshDatabase;

    private function pay(Sponsor $sponsor, int $amount): void
    {
        FundIncome::factory()->create([
            'source' => FundIncomeSource::Sponsorship,
            'amount' => $amount,
            'sponsor_id' => $sponsor->id,
        ]);
    }

    public function test_home_page_shows_only_active_sponsors_with_logos(): void
    {
        Sponsor::factory()->create(['name' => 'حامی فعال تست', 'logo_path' => 'images/sponsors/kavir.svg']);
        Sponsor::factory()->pending()->create(['name' => 'حامی در انتظار تست']);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('حامیان کوهستان', false)
            ->assertSee('حامی فعال تست', false)
            ->assertSee('images/sponsors/kavir.svg', false)
            ->assertDontSee('حامی در انتظار تست', false);
    }

    public function test_home_page_hides_the_section_without_sponsors(): void
    {
        $this->get(route('home'))->assertDontSee('sponsors-heading', false);
    }

    public function test_sponsors_page_lists_total_support_and_respects_hidden_amounts(): void
    {
        $shown = Sponsor::factory()->create(['name' => 'حامی نمایان', 'show_amount' => true]);
        $hidden = Sponsor::factory()->create(['name' => 'حامی بی‌نام‌ونشان', 'show_amount' => false]);
        $this->pay($shown, 30_000_000);
        $this->pay($hidden, 20_000_000);

        $this->get(route('sponsors.index'))
            ->assertOk()
            ->assertSee('مجموع حمایت‌های دریافت‌شده', false)
            ->assertSee(fa_num(50_000_000), false)
            ->assertSee(fa_num(30_000_000), false)
            ->assertDontSee(fa_num(20_000_000), false);

        $this->get(route('sponsors.show', $hidden))
            ->assertOk()
            ->assertSee('مبلغ حمایت را نمایش نمی‌دهد', false);
    }

    public function test_sponsor_page_shows_payments_and_links_with_sponsored_rel(): void
    {
        $sponsor = Sponsor::factory()->create(['website' => 'https://example.com/x', 'support_note' => 'حمایت از دوره مربیگری']);
        $this->pay($sponsor, 12_000_000);

        $this->get(route('sponsors.show', $sponsor))
            ->assertOk()
            ->assertSee(fa_num(12_000_000), false)
            ->assertSee('حمایت از دوره مربیگری', false)
            ->assertSee('rel="sponsored noopener noreferrer"', false);
    }

    public function test_pending_sponsor_page_is_not_public(): void
    {
        $this->get(route('sponsors.show', Sponsor::factory()->pending()->create()))->assertNotFound();
    }

    public function test_pricing_page_lists_packages_below_plans_with_minimum_amounts(): void
    {
        SponsorshipPackage::factory()->create(['name' => 'حامی تست طلایی', 'min_amount' => 150_000_000]);
        SponsorshipPackage::factory()->create(['name' => 'بسته خاموش', 'is_active' => false]);

        $this->get(route('pricing'))
            ->assertOk()
            ->assertSee('id="sponsorship"', false)
            ->assertSee('حامی تست طلایی', false)
            ->assertSee(fa_num(150_000_000), false)
            ->assertDontSee('بسته خاموش', false);
    }

    public function test_member_can_register_support_but_not_below_the_minimum(): void
    {
        $organization = User::factory()->shop()->create();
        $package = SponsorshipPackage::factory()->create(['min_amount' => 20_000_000]);

        Livewire::actingAs($organization)
            ->test(EditSponsorProfile::class)
            ->set('name', 'شرکت آزمایشی')
            ->set('summary', 'معرفی کوتاه')
            ->set('packageId', $package->id)
            ->set('pledgeAmount', 5_000_000)
            ->call('save')
            ->assertHasErrors('pledgeAmount');

        $this->assertSame(0, Sponsor::query()->count());

        Livewire::actingAs($organization)
            ->test(EditSponsorProfile::class)
            ->set('name', 'شرکت آزمایشی')
            ->set('summary', 'معرفی کوتاه')
            ->set('packageId', $package->id)
            ->set('pledgeAmount', 25_000_000)
            ->call('save')
            ->assertHasNoErrors();

        $sponsor = Sponsor::query()->firstOrFail();
        $this->assertSame($organization->id, $sponsor->user_id);
        $this->assertSame(SponsorStatus::Pending, $sponsor->status);
        $this->assertSame(25_000_000, $sponsor->pledge_amount);
    }

    public function test_sponsorship_panel_requires_login_and_no_organization_role_remains(): void
    {
        $this->get(route('panel.sponsorship'))->assertRedirect(route('login'));

        $this->actingAs(User::factory()->club()->create())
            ->get(route('panel.sponsorship'))
            ->assertOk();

        $this->assertNull(UserRole::tryFrom('organization'));
        $this->assertCount(6, UserRole::cases());
    }

    public function test_admin_sees_paid_total_and_can_link_income_to_a_sponsor(): void
    {
        $admin = User::factory()->admin()->create();
        $sponsor = Sponsor::factory()->create(['name' => 'حامی ادمین‌پسند', 'pledge_amount' => 40_000_000]);

        $this->actingAs($admin)
            ->post(route('panel.admin.resources.store', 'incomes'), [
                'source' => 'sponsorship',
                'amount' => 15000000,
                'received_at' => '2026-10-01',
                'sponsor_id' => $sponsor->id,
            ])
            ->assertRedirect(route('panel.admin.resources.index', 'incomes'));

        $this->actingAs($admin)
            ->get(route('panel.admin.resources.index', 'sponsors'))
            ->assertOk()
            ->assertSee('حامی ادمین‌پسند', false)
            ->assertSee(fa_num(15_000_000), false);
    }

    public function test_seeder_is_idempotent_and_marquee_clones_are_hidden_from_assistive_tech(): void
    {
        $this->seed(SponsorSeeder::class);
        $this->seed(SponsorSeeder::class);

        $this->assertSame(8, Sponsor::query()->count());
        $this->assertSame(3, SponsorshipPackage::query()->count());
        $this->assertSame(8, FundIncome::query()->whereNotNull('sponsor_id')->count());

        $html = $this->get(route('home'))->getContent();
        $this->assertStringContainsString('logo-marquee', $html);
        $this->assertStringContainsString('aria-hidden="true" tabindex="-1"', $html);
    }
}
