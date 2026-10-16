<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Club;
use App\Models\User;
use App\Support\PanelRegistry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PanelAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_admin_panel_to_login(): void
    {
        $this->get(route('panel.admin.dashboard'))
            ->assertRedirect(route('login'));
    }

    public function test_admin_can_open_admin_panel_and_users(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('panel.admin.dashboard'))
            ->assertOk()
            ->assertSee('پنل مدیر', false);

        $this->actingAs($admin)
            ->get(route('panel.admin.users.index'))
            ->assertOk()
            ->assertSee('کاربران', false);
    }

    public function test_climber_cannot_open_admin_panel(): void
    {
        $climber = User::factory()->create(['role' => UserRole::Climber]);

        $this->actingAs($climber)
            ->get(route('panel.admin.dashboard'))
            ->assertRedirect(route('panel.climber.dashboard'));
    }

    public function test_each_role_reaches_its_dedicated_panel_path(): void
    {
        foreach (PanelRegistry::all() as $panel) {
            $user = User::factory()->create(['role' => $panel['role']]);

            $this->actingAs($user)
                ->get($panel['path'])
                ->assertOk();
        }
    }

    public function test_demo_switcher_logs_into_selected_account(): void
    {
        $admin = User::factory()->admin()->create([
            'mobile' => '09120000001',
            'username' => 'admin',
        ]);
        User::factory()->create([
            'mobile' => '09120000010',
            'username' => 'climber',
            'role' => UserRole::Climber,
        ]);

        $this->post(route('dev.switch-demo-account'), [
            'mobile' => '09120000001',
        ])
            ->assertRedirect(route('panel.admin.dashboard'));

        $this->assertAuthenticatedAs($admin);
    }

    public function test_admin_can_update_user_role(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create([
            'mobile' => '09123334455',
            'username' => 'sample-user',
            'role' => UserRole::Climber,
        ]);

        $this->actingAs($admin)
            ->put(route('panel.admin.users.update', $user), [
                'name' => 'کاربر ویرایش‌شده',
                'mobile' => $user->mobile,
                'username' => $user->username,
                'role' => UserRole::Coach->value,
            ])
            ->assertRedirect(route('panel.admin.users.index'));

        $this->assertSame(UserRole::Coach, $user->fresh()->role);
        $this->assertSame('کاربر ویرایش‌شده', $user->fresh()->name);
    }

    public function test_legacy_admin_reviews_path_redirects_to_panel(): void
    {
        $this->get('/admin/reviews')
            ->assertRedirect('/panel/admin/reviews');
    }

    public function test_club_owner_can_open_club_panel(): void
    {
        $owner = User::factory()->create(['role' => UserRole::Club]);
        Club::factory()->create(['owner_id' => $owner->id]);

        $this->actingAs($owner)
            ->get(route('panel.club.dashboard'))
            ->assertOk()
            ->assertSee('پنل باشگاه', false);
    }
}
