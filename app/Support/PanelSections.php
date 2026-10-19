<?php

namespace App\Support;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * The workspaces a signed-in account can open in the panel: one per owned club or shop, the coach/leader
 * profile, and the personal climber section every non-admin account has.
 */
class PanelSections
{
    /**
     * @return Collection<int, array{role: UserRole, entity: ?Model, title: string, subtitle: string, icon: string, dashboard: string, edit: ?string, public: ?string}>
     */
    public static function for(User $user): Collection
    {
        $sections = collect();

        foreach ($user->roles() as $role) {
            match ($role) {
                UserRole::Admin => $sections->push(self::section($role, null, 'مدیریت سایت', route($role->dashboardRoute()))),
                UserRole::Club => $user->ownedClubs->each(fn ($club) => $sections->push(self::section(
                    $role,
                    $club,
                    $club->name,
                    route($role->dashboardRoute(), $club),
                    route($role->profileRoute(), $club),
                    route('clubs.show', $club),
                ))),
                UserRole::Shop => $user->shops->each(fn ($shop) => $sections->push(self::section(
                    $role,
                    $shop,
                    $shop->name,
                    route($role->dashboardRoute(), $shop),
                    route($role->profileRoute(), $shop),
                    route('shops.show', $shop),
                ))),
                UserRole::Coach, UserRole::Leader => $sections->push(self::section(
                    $role,
                    $user->coachProfile,
                    'پروفایل '.$role->label(),
                    route($role->dashboardRoute()),
                    route($role->profileRoute()),
                    route('coaches.show', $user->coachProfile),
                )),
                UserRole::Climber => $sections->push(self::section($role, null, 'فعالیت و عضویت من', route($role->dashboardRoute()))),
            };
        }

        return $sections;
    }

    /**
     * @return array{role: UserRole, entity: ?Model, title: string, subtitle: string, icon: string, dashboard: string, edit: ?string, public: ?string}
     */
    private static function section(UserRole $role, ?Model $entity, string $title, string $dashboard, ?string $edit = null, ?string $public = null): array
    {
        return [
            'role' => $role,
            'entity' => $entity,
            'title' => $title,
            'subtitle' => $role->label(),
            'icon' => $role->icon(),
            'dashboard' => $dashboard,
            'edit' => $edit,
            'public' => $public,
        ];
    }
}
