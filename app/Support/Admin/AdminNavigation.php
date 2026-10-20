<?php

namespace App\Support\Admin;

use Modules\Review\Models\Review;

class AdminNavigation
{
    /**
     * @var array<string, string>
     */
    private const GROUP_ICONS = [
        'site' => 'photo',
        'directory' => 'building-office-2',
        'geography' => 'map',
        'activity' => 'calendar-days',
        'peaks' => 'trophy',
        'projects' => 'rocket-launch',
        'finance' => 'banknotes',
    ];

    /**
     * @return list<array{key: string, heading: string, icon: string, expanded: bool, badge: ?string, items: list<array{label: string, icon: string, href: string, current: bool, badge: ?string}>}>
     */
    public static function groups(): array
    {
        $pendingReviews = Review::query()->pending()->count();
        $badge = $pendingReviews > 0 ? fa_num($pendingReviews) : null;
        $currentResource = request()->route('resource');

        $groups = [[
            'key' => 'people',
            'heading' => 'کاربران و نظرات',
            'icon' => 'users',
            'badge' => $badge,
            'items' => [
                self::item('کاربران', 'users', route('panel.admin.users.index'), request()->routeIs('panel.admin.users.*')),
                self::item('نقش‌ها و دسترسی‌ها', 'key', route('panel.admin.roles'), request()->routeIs('panel.admin.roles')),
                self::item('نظرات کاربران', 'chat-bubble-left-right', route('panel.admin.reviews'), request()->routeIs('panel.admin.reviews'), $badge),
            ],
        ]];

        foreach (AdminResources::grouped() as $key => $resources) {
            $items = array_map(
                fn (AdminResource $resource): array => self::item(
                    $resource->plural(),
                    $resource->icon(),
                    route('panel.admin.resources.index', $resource->key()),
                    $currentResource === $resource->key(),
                ),
                $resources,
            );

            if ($key === 'geography') {
                $items[] = self::item('لینک و QR نقشه', 'qr-code', route('panel.admin.map-links'), request()->routeIs('panel.admin.map-links'));
            }

            if ($key === 'finance') {
                array_unshift($items, self::item('نمای کلی مالی', 'chart-pie', route('panel.admin.finance'), request()->routeIs('panel.admin.finance')));
            }

            $groups[] = [
                'key' => $key,
                'heading' => AdminResources::GROUPS[$key],
                'icon' => self::GROUP_ICONS[$key] ?? 'folder',
                'badge' => null,
                'items' => $items,
            ];
        }

        return array_map(function (array $group): array {
            $group['expanded'] = collect($group['items'])->contains('current', true);

            return $group;
        }, $groups);
    }

    /**
     * @return array{label: string, icon: string, href: string, current: bool, badge: ?string}
     */
    private static function item(string $label, string $icon, string $href, bool $current, ?string $badge = null): array
    {
        return compact('label', 'icon', 'href', 'current', 'badge');
    }
}
