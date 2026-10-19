<?php

namespace App\Support;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Collection;

class PanelRegistry
{
    /**
     * @return array<string, array{role: UserRole, label: string, home: string, path: string, demo_mobile: string, demo_username: string}>
     */
    public static function all(): array
    {
        return [
            'admin' => [
                'role' => UserRole::Admin,
                'label' => 'پنل مدیر',
                'home' => 'panel.admin.dashboard',
                'path' => '/panel/admin',
                'demo_mobile' => '09120000001',
                'demo_username' => 'admin',
            ],
            'climber' => [
                'role' => UserRole::Climber,
                'label' => 'پنل کوهنورد',
                'home' => 'panel.climber.dashboard',
                'path' => '/panel/climber',
                'demo_mobile' => '09120000010',
                'demo_username' => 'climber',
            ],
            'club' => [
                'role' => UserRole::Club,
                'label' => 'پنل باشگاه',
                'home' => 'panel.club.dashboard',
                'path' => '/panel/club',
                'demo_mobile' => '09120000002',
                'demo_username' => 'club-kerman',
            ],
            'coach' => [
                'role' => UserRole::Coach,
                'label' => 'پنل مربی',
                'home' => 'panel.coach.dashboard',
                'path' => '/panel/coach',
                'demo_mobile' => '09120000006',
                'demo_username' => 'sara-mohammadi',
            ],
            'leader' => [
                'role' => UserRole::Leader,
                'label' => 'پنل سرپرست',
                'home' => 'panel.leader.dashboard',
                'path' => '/panel/leader',
                'demo_mobile' => '09120000005',
                'demo_username' => 'ali-rezaei',
            ],
            'shop' => [
                'role' => UserRole::Shop,
                'label' => 'پنل فروشگاه',
                'home' => 'panel.shop.dashboard',
                'path' => '/panel/shop',
                'demo_mobile' => '09120000003',
                'demo_username' => 'shop-kouhestan',
            ],
        ];
    }

    /**
     * @return array{role: UserRole, label: string, home: string, path: string, demo_mobile: string, demo_username: string}|null
     */
    public static function forRole(UserRole $role): ?array
    {
        foreach (self::all() as $panel) {
            if ($panel['role'] === $role) {
                return $panel;
            }
        }

        return null;
    }

    /**
     * Admins land on their own panel; a plain climber on their personal dashboard; everyone else on the
     * hub that gathers all of their sections.
     */
    public static function homeRouteFor(User $user): string
    {
        return match (true) {
            $user->isAdmin() => 'panel.admin.dashboard',
            $user->roles()->count() === 1 => UserRole::Climber->dashboardRoute(),
            default => 'panel.home',
        };
    }

    public static function pathFor(User $user): string
    {
        return match (true) {
            $user->isAdmin() => self::forRole(UserRole::Admin)['path'],
            $user->roles()->count() === 1 => self::forRole(UserRole::Climber)['path'],
            default => '/panel',
        };
    }

    /**
     * Mobile of the seeded demo account that holds several roles at once (club, coach and shop).
     */
    public const MULTI_ROLE_DEMO_MOBILE = '09120000004';

    /**
     * @return Collection<int, array{mobile: string, username: string, name: string, role: ?UserRole, label: string, path: string, home: string}>
     */
    public static function demoAccounts(): Collection
    {
        $accounts = collect(self::all())
            ->map(function (array $panel): array {
                $user = User::query()->where('mobile', $panel['demo_mobile'])->first();

                return [
                    'mobile' => $panel['demo_mobile'],
                    'username' => $panel['demo_username'],
                    'name' => $user?->name ?? $panel['label'],
                    'role' => $panel['role'],
                    'label' => $panel['label'],
                    'path' => $panel['path'],
                    'home' => $panel['home'],
                ];
            });

        $multi = User::query()->where('mobile', self::MULTI_ROLE_DEMO_MOBILE)->first();

        return $accounts
            ->push([
                'mobile' => self::MULTI_ROLE_DEMO_MOBILE,
                'username' => 'multi-role',
                'name' => $multi?->name ?? 'کاربر چندنقشه',
                'role' => null,
                'label' => 'پنل چندنقشه',
                'path' => '/panel',
                'home' => 'panel.home',
            ])
            ->values();
    }

    public static function switcherEnabled(): bool
    {
        return app()->environment(['local', 'testing']) || (bool) config('app.debug');
    }
}
