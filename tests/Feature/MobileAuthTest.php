<?php

namespace Tests\Feature;

use App\Models\User;
use App\Support\MobileNumber;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MobileAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_requires_mobile_and_username(): void
    {
        $this->post(route('register.store'), [
            'name' => 'کاربر جدید',
            'mobile' => '09121112233',
            'username' => 'new-climber',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])
            ->assertRedirect(route('panel.climber.dashboard'));

        $this->assertDatabaseHas('users', [
            'mobile' => '09121112233',
            'username' => 'new-climber',
            'is_admin' => false,
        ]);
    }

    public function test_mobile_must_be_unique(): void
    {
        User::factory()->create([
            'mobile' => '09121112233',
            'username' => 'existing',
        ]);

        $this->from(route('register'))
            ->post(route('register.store'), [
                'name' => 'کاربر تکراری',
                'mobile' => '09121112233',
                'username' => 'another-user',
                'password' => 'password',
                'password_confirmation' => 'password',
            ])
            ->assertRedirect(route('register'))
            ->assertSessionHasErrors('mobile');
    }

    public function test_mobile_number_helper_normalizes_iranian_formats(): void
    {
        $this->assertSame('09123456789', MobileNumber::normalize('9123456789'));
        $this->assertSame('09123456789', MobileNumber::normalize('+989123456789'));
        $this->assertSame('09123456789', MobileNumber::normalize('00989123456789'));
        $this->assertTrue(MobileNumber::isValid('09123456789'));
        $this->assertFalse(MobileNumber::isValid('08123456789'));
    }
}
