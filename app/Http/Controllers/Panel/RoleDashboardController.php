<?php

namespace App\Http\Controllers\Panel;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Support\PanelRegistry;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RoleDashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();
        $panel = PanelRegistry::forRole($user->role);

        $user->loadMissing(['primaryClub', 'coachProfile.club', 'shop', 'clubs']);

        return view('panel.role-dashboard', [
            'user' => $user,
            'panel' => $panel,
            'role' => $user->role,
            'links' => $this->linksFor($user->role),
        ]);
    }

    /**
     * @return list<array{label: string, route: string, params?: array<string, mixed>}>
     */
    private function linksFor(UserRole $role): array
    {
        $common = [
            ['label' => 'تنظیمات حساب', 'route' => 'profile.edit'],
            ['label' => 'امنیت / رمز عبور', 'route' => 'security.edit'],
        ];

        return match ($role) {
            UserRole::Climber => [
                ...$common,
                ['label' => 'مقاصد', 'route' => 'destinations.index'],
                ['label' => 'باشگاه‌ها', 'route' => 'clubs.index'],
            ],
            UserRole::Club => [
                ...$common,
                ['label' => 'ویرایش باشگاه', 'route' => 'panel.club.profile'],
            ],
            UserRole::Coach => [
                ...$common,
                ['label' => 'ویرایش پروفایل مربی', 'route' => 'panel.coach.profile'],
            ],
            UserRole::Leader => [
                ...$common,
                ['label' => 'ویرایش پروفایل سرپرست', 'route' => 'panel.leader.profile'],
            ],
            UserRole::Shop => [
                ...$common,
                ['label' => 'ویرایش فروشگاه', 'route' => 'panel.shop.profile'],
            ],
            UserRole::Organization => $common,
            UserRole::Admin => [
                ['label' => 'کاربران', 'route' => 'panel.admin.users.index'],
                ['label' => 'نظرات', 'route' => 'panel.admin.reviews'],
                ...$common,
            ],
        };
    }
}
