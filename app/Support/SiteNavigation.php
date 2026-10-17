<?php

namespace App\Support;

class SiteNavigation
{
    /**
     * Public sections of the site, shared by the header, footer, home page and breadcrumbs.
     *
     * @return array<string, array{label: string, route: string, icon: string, image: string, primary: bool, description: string}>
     */
    public static function sections(): array
    {
        return [
            'regions' => [
                'label' => 'مناطق',
                'route' => 'regions.index',
                'icon' => 'compass',
                'image' => 'cover-06.jpg',
                'primary' => false,
                'description' => 'مناطق کوهستانی استان',
            ],
            'destinations' => [
                'label' => 'مقاصد',
                'route' => 'destinations.index',
                'icon' => 'mountain',
                'image' => 'cover-01.jpg',
                'primary' => true,
                'description' => 'قله‌ها و نقاط طبیعی',
            ],
            'attractions' => [
                'label' => 'جاذبه‌ها',
                'route' => 'attractions.index',
                'icon' => 'star',
                'image' => 'cover-05.jpg',
                'primary' => false,
                'description' => 'مکان‌های دیدنی کنار مسیر',
            ],
            'clubs' => [
                'label' => 'باشگاه‌ها',
                'route' => 'clubs.index',
                'icon' => 'users',
                'image' => 'cover-02.jpg',
                'primary' => true,
                'description' => 'باشگاه‌های رسمی کوهنوردی',
            ],
            'coaches' => [
                'label' => 'مربیان',
                'route' => 'coaches.index',
                'icon' => 'academic',
                'image' => 'cover-03.jpg',
                'primary' => false,
                'description' => 'مربیان و سرپرستان معتبر',
            ],
            'programs' => [
                'label' => 'برنامه‌ها',
                'route' => 'programs.index',
                'icon' => 'calendar',
                'image' => 'cover-04.jpg',
                'primary' => true,
                'description' => 'صعودها و تورهای پیش‌رو',
            ],
            'accommodations' => [
                'label' => 'اقامتگاه‌ها',
                'route' => 'accommodations.index',
                'icon' => 'home',
                'image' => 'cover-lodge.jpg',
                'primary' => false,
                'description' => 'بوم‌گردی، هتل و مهمان‌سرا',
            ],
            'shelters' => [
                'label' => 'پناهگاه‌ها',
                'route' => 'shelters.index',
                'icon' => 'shelter',
                'image' => 'cover-camp.jpg',
                'primary' => false,
                'description' => 'نقاط حمایتی مسیرهای کوهستانی',
            ],
            'shops' => [
                'label' => 'فروشگاه‌ها',
                'route' => 'shops.index',
                'icon' => 'bag',
                'image' => 'cover-09.jpg',
                'primary' => false,
                'description' => 'تجهیزات کوهنوردی',
            ],
            'map' => [
                'label' => 'نقشه',
                'route' => 'map',
                'icon' => 'map',
                'image' => 'cover-08.jpg',
                'primary' => true,
                'description' => 'همه چیز روی یک نقشه',
            ],
        ];
    }

    /**
     * @return array{label: string, route: string, icon: string, image: string, primary: bool, description: string}
     */
    public static function section(string $key): array
    {
        return self::sections()[$key];
    }
}
