<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\MembershipDue;
use App\Models\MembershipPlan;
use App\Models\User;
use Database\Seeders\MembershipPlanSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MembershipPlanSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_is_idempotent_and_covers_every_group_and_payment_state(): void
    {
        $this->seed(MembershipPlanSeeder::class);
        $this->seed(MembershipPlanSeeder::class);

        $this->assertSame(MembershipPlan::query()->count(), MembershipPlan::query()->distinct('name')->count('name'));

        foreach (UserRole::cases() as $role) {
            if ($role !== UserRole::Admin) {
                $this->assertTrue(MembershipPlan::query()->active()->where('role', $role->value)->exists(), $role->value);
            }
        }

        $this->assertSame(3, MembershipPlan::query()->active()->where('role', UserRole::Shop->value)->count());
        $this->assertTrue(MembershipPlan::query()->where('is_active', false)->exists());
        $this->assertSame(10, User::query()->where('username', 'like', 'sample-%')->count());
        $this->assertSame(9, MembershipDue::query()->count());

        $settled = User::query()->where('username', 'sample-coach-silver')->firstOrFail();
        $partial = User::query()->where('username', 'sample-leader-partial')->firstOrFail();
        $overdue = User::query()->where('username', 'sample-club-overdue')->firstOrFail();

        $this->assertSame('مربی همراه', $settled->currentMembershipPlan()?->badge_label);
        $this->assertNull($partial->currentMembershipPlan());
        $this->assertTrue($overdue->membershipDues()->firstOrFail()->isOverdue());
    }
}
