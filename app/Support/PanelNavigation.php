<?php

namespace App\Support;

use App\Enums\UserRole;
use App\Models\User;

class PanelNavigation
{
    /**
     * @return list<array{heading: string, items: list<array{label: string, icon: string, href: string, current: bool, external: bool}>}>
     */
    public static function groups(User $user): array
    {
        $role = $user->role;
        $mine = [
            self::item('داشبورد', 'squares-2x2', route($user->panelHomeRoute()), request()->routeIs('panel.'.$role->value.'.dashboard')),
        ];

        match ($role) {
            UserRole::Club => array_push(
                $mine,
                self::item('ویرایش باشگاه', 'building-office-2', route('panel.club.profile'), request()->routeIs('panel.club.profile')),
                ...self::publicLink('صفحه عمومی باشگاه', 'clubs.show', $user->ownedClubs()->first() ?? $user->primaryClub),
            ),
            UserRole::Coach, UserRole::Leader => array_push(
                $mine,
                self::item('پروفایل تخصصی', 'academic-cap', route('panel.'.$role->value.'.profile'), request()->routeIs('panel.'.$role->value.'.profile')),
                ...self::publicLink('صفحه عمومی من', 'coaches.show', $user->coachProfile),
            ),
            UserRole::Shop => array_push(
                $mine,
                self::item('ویرایش فروشگاه', 'shopping-bag', route('panel.shop.profile'), request()->routeIs('panel.shop.profile')),
                ...self::publicLink('صفحه عمومی فروشگاه', 'shops.show', $user->shop),
            ),
            default => null,
        };

        if ($role->hasPublicDirectoryProfile()) {
            $mine[] = self::item('پروفایل عمومی', 'user-circle', route('profiles.show', $user), false, true);
        }

        return [
            ['heading' => 'پنل '.$role->label(), 'items' => $mine],
            ['heading' => 'حساب کاربری', 'items' => [
                self::item('اطلاعات حساب', 'user', route('profile.edit'), request()->routeIs('profile.edit')),
                self::item('امنیت و رمز عبور', 'lock-closed', route('security.edit'), request()->routeIs('security.edit')),
                self::item('ظاهر', 'paint-brush', route('appearance.edit'), request()->routeIs('appearance.edit')),
            ]],
            ['heading' => 'کاوش در سایت', 'items' => [
                self::item('صفحه اصلی', 'globe-alt', route('home'), false, true),
                self::item('نقشه', 'map', route('map'), false, true),
                self::item('مقاصد', 'flag', route('destinations.index'), false, true),
                self::item('باشگاه‌ها', 'building-office-2', route('clubs.index'), false, true),
                self::item('برنامه‌ها', 'calendar-days', route('programs.index'), false, true),
            ]],
        ];
    }

    /**
     * @return array{label: string, icon: string, href: string, current: bool, external: bool}
     */
    private static function item(string $label, string $icon, string $href, bool $current, bool $external = false): array
    {
        return compact('label', 'icon', 'href', 'current', 'external');
    }

    /**
     * @return list<array{label: string, icon: string, href: string, current: bool, external: bool}>
     */
    private static function publicLink(string $label, string $route, mixed $model): array
    {
        return $model === null ? [] : [self::item($label, 'arrow-top-right-on-square', route($route, $model), false, true)];
    }
}
