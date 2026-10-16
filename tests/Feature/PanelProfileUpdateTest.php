<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Livewire\Panel\EditClubProfile;
use App\Livewire\Panel\EditCoachProfile;
use App\Livewire\Panel\EditShopProfile;
use App\Models\Club;
use App\Models\CoachProfile;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PanelProfileUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_update_settings_profile(): void
    {
        $user = User::factory()->create([
            'name' => 'نام قبلی',
            'mobile' => '09120000011',
            'username' => 'old-user',
            'role' => UserRole::Climber,
        ]);

        $this->actingAs($user)
            ->get(route('profile.edit'))
            ->assertOk();

        Livewire::actingAs($user)
            ->test('pages::settings.profile')
            ->set('name', 'نام جدید')
            ->set('mobile', '09120000012')
            ->set('username', 'new-user')
            ->call('updateProfileInformation')
            ->assertHasNoErrors();

        $this->assertSame('نام جدید', $user->fresh()->name);
        $this->assertSame('09120000012', $user->fresh()->mobile);
        $this->assertSame('new-user', $user->fresh()->username);
    }

    public function test_club_owner_can_update_club_profile(): void
    {
        $owner = User::factory()->create(['role' => UserRole::Club]);
        $club = Club::factory()->create([
            'owner_id' => $owner->id,
            'name' => 'باشگاه قدیم',
        ]);

        Livewire::actingAs($owner)
            ->test(EditClubProfile::class)
            ->set('name', 'باشگاه جدید')
            ->set('summary', 'خلاصه به‌روز')
            ->set('description', 'توضیحات کامل باشگاه برای به‌روزرسانی تست.')
            ->set('city', 'کرمان')
            ->set('address', 'آدرس جدید')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame('باشگاه جدید', $club->fresh()->name);
        $this->assertSame('آدرس جدید', $club->fresh()->address);
    }

    public function test_coach_can_update_coach_profile(): void
    {
        $user = User::factory()->create(['role' => UserRole::Coach]);
        $profile = CoachProfile::factory()->create([
            'user_id' => $user->id,
            'specialty' => 'قدیمی',
        ]);

        Livewire::actingAs($user)
            ->test(EditCoachProfile::class)
            ->set('headline', 'عنوان جدید')
            ->set('bio', 'بیوگرافی به‌روز شده برای مربی.')
            ->set('specialty', 'صخره‌نوردی')
            ->set('experience_years', 12)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame('صخره‌نوردی', $profile->fresh()->specialty);
        $this->assertSame(12, $profile->fresh()->experience_years);
    }

    public function test_shop_owner_can_update_shop_profile(): void
    {
        $user = User::factory()->create(['role' => UserRole::Shop]);
        $shop = Shop::factory()->create([
            'user_id' => $user->id,
            'name' => 'فروشگاه قدیم',
        ]);

        Livewire::actingAs($user)
            ->test(EditShopProfile::class)
            ->set('name', 'فروشگاه جدید')
            ->set('summary', 'خلاصه فروشگاه جدید')
            ->set('city', 'کرمان')
            ->set('address', 'خیابان جدید')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame('فروشگاه جدید', $shop->fresh()->name);
    }
}
