<?php

namespace App\Support;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class PanelNavigation
{
    /**
     * @return list<array{heading: string, items: list<array{label: string, icon: string, href: string, current: bool, external: bool}>}>
     */
    public static function groups(User $user): array
    {
        $sections = PanelSections::for($user);
        $groups = [];

        if ($sections->count() > 1) {
            $groups[] = ['heading' => 'پنل من', 'items' => [
                self::item('همه بخش‌های من', 'squares-2x2', route('panel.home'), request()->routeIs('panel.home')),
            ]];
        }

        foreach ($sections->reject(fn (array $section): bool => $section['role'] === UserRole::Climber) as $section) {
            $groups[] = ['heading' => $section['title'], 'items' => self::sectionItems($section)];
        }

        $groups[] = ['heading' => $sections->count() > 1 ? 'فعالیت و عضویت من' : 'پنل '.$user->roleLabel(), 'items' => self::personalItems($user)];

        return [
            ...$groups,
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
     * @param  array{role: UserRole, entity: ?Model, title: string, subtitle: string, icon: string, dashboard: string, edit: ?string, public: ?string}  $section
     * @return list<array{label: string, icon: string, href: string, current: bool, external: bool}>
     */
    private static function sectionItems(array $section): array
    {
        $role = $section['role'];
        $editLabel = match ($role) {
            UserRole::Club => 'ویرایش باشگاه',
            UserRole::Shop => 'ویرایش فروشگاه',
            default => 'پروفایل تخصصی',
        };

        return [
            self::item('داشبورد', 'squares-2x2', $section['dashboard'], self::isCurrent($section, $role->dashboardRoute())),
            self::item($editLabel, $role->icon(), $section['edit'], self::isCurrent($section, $role->profileRoute())),
            ...self::publicLink('صفحه عمومی', $section['public']),
        ];
    }

    /**
     * @return list<array{label: string, icon: string, href: string, current: bool, external: bool}>
     */
    private static function personalItems(User $user): array
    {
        $items = [
            self::item('داشبورد شخصی', 'user', route('panel.climber.dashboard'), request()->routeIs('panel.climber.dashboard')),
            self::item('صندوق و امور مالی', 'banknotes', route('panel.finance'), request()->routeIs('panel.finance')),
            self::item('پلن‌های عضویت', 'sparkles', route('panel.membership'), request()->routeIs('panel.membership')),
            self::item('حمایت از کوهستان', 'building-library', route('panel.sponsorship'), request()->routeIs('panel.sponsorship')),
        ];

        if ($user->hasDirectoryProfile()) {
            $items[] = self::item('پروفایل عمومی', 'user-circle', route('profiles.show', $user), false, true);
        }

        return $items;
    }

    /**
     * @param  array{role: UserRole, entity: ?Model, title: string, subtitle: string, icon: string, dashboard: string, edit: ?string, public: ?string}  $section
     */
    private static function isCurrent(array $section, string $route): bool
    {
        if (! request()->routeIs($route)) {
            return false;
        }

        $routeEntity = request()->route(match ($section['role']) {
            UserRole::Club => 'club',
            UserRole::Shop => 'shop',
            default => 'none',
        });

        return $routeEntity === null || $section['entity']?->is($routeEntity) === true;
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
    private static function publicLink(string $label, ?string $href): array
    {
        return $href === null ? [] : [self::item($label, 'arrow-top-right-on-square', $href, false, true)];
    }
}
