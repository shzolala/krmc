<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Club;
use App\Models\Destination;
use App\Models\User;
use App\Support\Admin\AdminResources;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Review\Models\Review;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_admin_cannot_open_content_management(): void
    {
        $climber = User::factory()->create(['role' => UserRole::Climber]);

        $this->actingAs($climber)
            ->get(route('panel.admin.resources.index', 'clubs'))
            ->assertRedirect(route('panel.climber.dashboard'));

        $this->actingAs($climber)
            ->get(route('panel.admin.roles'))
            ->assertRedirect(route('panel.climber.dashboard'));
    }

    public function test_dashboard_and_roles_pages_render_for_admin(): void
    {
        $admin = User::factory()->admin()->create();
        Club::factory()->create();
        Review::factory()->forReviewable(Club::factory()->create())->pending()->create();

        $this->actingAs($admin)->get(route('panel.admin.dashboard'))
            ->assertOk()
            ->assertSee('نیازمند توجه', false);

        $this->actingAs($admin)->get(route('panel.admin.roles'))
            ->assertOk()
            ->assertSee('ماتریس دسترسی‌ها', false);
    }

    public function test_every_resource_list_and_create_page_renders(): void
    {
        $admin = User::factory()->admin()->create();

        foreach (AdminResources::all() as $resource) {
            $resource->model()::factory()->create();

            $this->actingAs($admin)
                ->get(route('panel.admin.resources.index', $resource->key()))
                ->assertOk();

            $this->actingAs($admin)
                ->get(route('panel.admin.resources.create', $resource->key()))
                ->assertOk();

            $this->actingAs($admin)
                ->get(route('panel.admin.resources.edit', ['resource' => $resource->key(), 'id' => $resource->model()::query()->firstOrFail()->getKey()]))
                ->assertOk();
        }
    }

    public function test_unknown_resource_returns_not_found(): void
    {
        $this->actingAs(User::factory()->admin()->create())
            ->get('/panel/admin/manage/unknown')
            ->assertNotFound();
    }

    public function test_admin_can_create_resource_with_generated_slug(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->post(route('panel.admin.resources.store', 'clubs'), [
                'name' => 'باشگاه تازه',
                'city' => 'کرمان',
                'is_verified' => '1',
            ])
            ->assertRedirect(route('panel.admin.resources.index', 'clubs'));

        $club = Club::query()->where('name', 'باشگاه تازه')->firstOrFail();
        $this->assertSame('bashgah-tazh', $club->slug);
        $this->assertTrue($club->is_verified);
        $this->assertFalse($club->is_featured);
    }

    public function test_resource_validation_rejects_missing_required_fields(): void
    {
        $this->actingAs(User::factory()->admin()->create())
            ->post(route('panel.admin.resources.store', 'clubs'), ['city' => 'کرمان'])
            ->assertSessionHasErrors('name');
    }

    public function test_admin_can_update_toggle_and_delete_resource(): void
    {
        $admin = User::factory()->admin()->create();
        $destination = Destination::factory()->create(['is_featured' => false]);

        $this->actingAs($admin)
            ->post(route('panel.admin.resources.toggle', ['resource' => 'destinations', 'id' => $destination->id, 'field' => 'is_featured']))
            ->assertRedirect();
        $this->assertTrue($destination->fresh()->is_featured);

        $this->actingAs($admin)
            ->post(route('panel.admin.resources.toggle', ['resource' => 'destinations', 'id' => $destination->id, 'field' => 'name']))
            ->assertNotFound();

        $this->actingAs($admin)
            ->put(route('panel.admin.resources.update', ['resource' => 'destinations', 'id' => $destination->id]), [
                'name' => 'نام جدید مقصد',
                'slug' => $destination->slug,
                'catalog_id' => $destination->catalog_id,
            ])
            ->assertRedirect(route('panel.admin.resources.index', 'destinations'));
        $this->assertSame('نام جدید مقصد', $destination->fresh()->name);

        $this->actingAs($admin)
            ->delete(route('panel.admin.resources.destroy', ['resource' => 'destinations', 'id' => $destination->id]))
            ->assertRedirect(route('panel.admin.resources.index', 'destinations'));
        $this->assertModelMissing($destination);
    }

    public function test_resource_list_supports_search_and_filters(): void
    {
        $admin = User::factory()->admin()->create();
        Club::factory()->create(['name' => 'باشگاه آلفا', 'is_verified' => true]);
        Club::factory()->create(['name' => 'باشگاه بتا', 'is_verified' => false]);

        $this->actingAs($admin)
            ->get(route('panel.admin.resources.index', ['resource' => 'clubs', 'q' => 'آلفا']))
            ->assertSee('باشگاه آلفا', false)
            ->assertDontSee('باشگاه بتا', false);

        $this->actingAs($admin)
            ->get(route('panel.admin.resources.index', ['resource' => 'clubs', 'is_verified' => 0]))
            ->assertSee('باشگاه بتا', false)
            ->assertDontSee('باشگاه آلفا', false);
    }

    public function test_admin_can_create_and_delete_users(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->post(route('panel.admin.users.store'), [
                'name' => 'مربی تازه',
                'mobile' => '09121112233',
                'username' => 'new-coach',
                'role' => UserRole::Coach->value,
                'password' => 'Str0ng-Passw0rd!',
            ])
            ->assertRedirect();

        $user = User::query()->where('username', 'new-coach')->firstOrFail();
        $this->assertSame(UserRole::Coach, $user->role);

        $this->actingAs($admin)
            ->delete(route('panel.admin.users.destroy', $user))
            ->assertRedirect(route('panel.admin.users.index'));
        $this->assertModelMissing($user);
    }

    public function test_admin_cannot_delete_or_demote_self(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->delete(route('panel.admin.users.destroy', $admin))
            ->assertSessionHas('error');
        $this->assertModelExists($admin);

        $this->actingAs($admin)
            ->put(route('panel.admin.users.update', $admin), [
                'name' => $admin->name,
                'mobile' => $admin->mobile,
                'username' => $admin->username,
                'role' => UserRole::Climber->value,
            ])
            ->assertSessionHasErrors('role');
        $this->assertSame(UserRole::Admin, $admin->fresh()->role);
    }

    public function test_admin_can_impersonate_and_return(): void
    {
        $admin = User::factory()->admin()->create();
        $club = User::factory()->create(['role' => UserRole::Club]);

        $this->actingAs($admin)
            ->post(route('panel.admin.users.impersonate', $club))
            ->assertRedirect(route('panel.club.dashboard'));
        $this->assertAuthenticatedAs($club);

        $this->post(route('panel.impersonation.stop'))
            ->assertRedirect(route('panel.admin.users.index'));
        $this->assertAuthenticatedAs($admin);
    }

    public function test_impersonation_cannot_target_other_admins_or_be_started_by_non_admins(): void
    {
        $admin = User::factory()->admin()->create();
        $otherAdmin = User::factory()->admin()->create();
        $climber = User::factory()->create();

        $this->actingAs($admin)
            ->post(route('panel.admin.users.impersonate', $otherAdmin))
            ->assertSessionHas('error');
        $this->assertAuthenticatedAs($admin);

        $this->actingAs($climber)
            ->post(route('panel.admin.users.impersonate', $admin))
            ->assertRedirect(route('panel.climber.dashboard'));
        $this->assertAuthenticatedAs($climber);
    }

    public function test_admin_can_switch_between_demo_roles_and_return(): void
    {
        $admin = User::factory()->admin()->create(['mobile' => '09120000001', 'username' => 'admin']);
        $coach = User::factory()->create(['mobile' => '09120000006', 'username' => 'sara-mohammadi', 'role' => UserRole::Coach]);
        $shop = User::factory()->create(['mobile' => '09120000003', 'username' => 'shop-kouhestan', 'role' => UserRole::Shop]);

        $this->actingAs($admin)
            ->get(route('panel.admin.dashboard'))
            ->assertSee('تغییر نقش نمایشی', false);

        $this->actingAs($admin)
            ->post(route('panel.impersonation.switch'), ['mobile' => $coach->mobile])
            ->assertRedirect(route('panel.coach.dashboard'));
        $this->assertAuthenticatedAs($coach);

        $this->post(route('panel.impersonation.switch'), ['mobile' => $shop->mobile])
            ->assertRedirect(route('panel.shop.dashboard'));
        $this->assertAuthenticatedAs($shop);

        $this->post(route('panel.impersonation.switch'), ['mobile' => $admin->mobile])
            ->assertRedirect(route('panel.admin.users.index'));
        $this->assertAuthenticatedAs($admin);
    }

    public function test_demo_role_switcher_is_hidden_and_blocked_for_non_admins(): void
    {
        User::factory()->admin()->create(['mobile' => '09120000001', 'username' => 'admin']);
        $climber = User::factory()->create(['mobile' => '09120000010', 'username' => 'climber']);

        $this->actingAs($climber)
            ->get(route('panel.climber.dashboard'))
            ->assertDontSee('تغییر نقش نمایشی', false)
            ->assertDontSee(route('dev.switch-demo-account'), false);

        $this->post(route('panel.impersonation.switch'), ['mobile' => '09120000001'])
            ->assertForbidden();
        $this->assertAuthenticatedAs($climber);
    }
}
