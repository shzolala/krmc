<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_is_accessible(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee('ورود به پنل', false)
            ->assertSee('شماره موبایل', false);
    }

    public function test_user_can_login_with_mobile_and_reaches_role_panel(): void
    {
        $admin = User::factory()->admin()->create([
            'mobile' => '09120000001',
            'username' => 'admin',
        ]);

        $this->post(route('login.store'), [
            'mobile' => '09120000001',
            'password' => 'password',
        ])
            ->assertRedirect(route('panel.admin.dashboard'));

        $this->assertAuthenticatedAs($admin);
    }

    public function test_login_normalizes_mobile_variants(): void
    {
        $user = User::factory()->create([
            'mobile' => '09123456789',
            'username' => 'climber-one',
            'role' => UserRole::Climber,
        ]);

        $this->post(route('login.store'), [
            'mobile' => '+98 912 345 6789',
            'password' => 'password',
        ])
            ->assertRedirect(route('panel.climber.dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_invalid_credentials_are_rejected(): void
    {
        User::factory()->create([
            'mobile' => '09120000010',
            'username' => 'climber',
        ]);

        $this->from(route('login'))
            ->post(route('login.store'), [
                'mobile' => '09120000010',
                'password' => 'wrong-password',
            ])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('mobile');

        $this->assertGuest();
    }

    public function test_user_can_logout(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('logout'))
            ->assertRedirect(route('home'));

        $this->assertGuest();
    }
}
