<?php

namespace App\Support;

use App\Enums\UserRole;
use App\Models\Club;
use App\Models\CoachProfile;
use App\Models\Destination;
use App\Models\Program;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Modules\Review\Enums\ReviewStatus;
use Modules\Review\Models\Review;

class PanelDashboard
{
    /**
     * Builds the dashboard payload of one panel section: a role together with the club, shop or profile it manages.
     *
     * @return array{
     *     role: UserRole,
     *     entity: ?Model,
     *     entityLabel: ?string,
     *     publicUrl: ?string,
     *     editRoute: ?string,
     *     badges: list<array{label: string, color: string}>,
     *     stats: list<array{label: string, value: string, icon: string, tone: string, hint: ?string, href: ?string}>,
     *     completeness: array{percent: int, items: list<array{label: string, done: bool}>},
     *     programsTitle: string,
     *     programs: Collection<int, Program>,
     *     reviewsTitle: string,
     *     reviews: Collection<int, Review>,
     *     pendingReviews: int,
     *     suggestions: Collection<int, Destination>,
     *     actions: list<array{label: string, icon: string, href: string, external: bool}>,
     *     siblings: list<array{label: string, href: string, current: bool}>
     * }
     */
    public static function for(User $user, UserRole $role, ?Model $entity = null): array
    {
        $entity ??= match ($role) {
            UserRole::Club => $user->ownedClubs->first(),
            UserRole::Coach, UserRole::Leader => $user->coachProfile,
            UserRole::Shop => $user->shops->first(),
            default => null,
        };

        $base = [
            'role' => $role,
            'entity' => $entity,
            'entityLabel' => null,
            'publicUrl' => null,
            'editRoute' => null,
            'badges' => [],
            'stats' => [],
            'completeness' => ['percent' => 0, 'items' => []],
            'programsTitle' => 'برنامه‌های پیش‌رو',
            'programs' => self::upcomingPrograms(),
            'reviewsTitle' => 'آخرین نظرات',
            'reviews' => collect(),
            'pendingReviews' => 0,
            'suggestions' => collect(),
            'actions' => [],
            'siblings' => self::siblings($user, $role, $entity),
        ];

        $payload = match (true) {
            $entity instanceof Club => self::club($user, $entity),
            $entity instanceof CoachProfile => self::coach($user, $entity),
            $entity instanceof Shop => self::shop($user, $entity),
            default => self::climber($user),
        };

        return [...$base, ...$payload];
    }

    /**
     * Quick links between the other clubs (or shops) the same account owns.
     *
     * @return list<array{label: string, href: string, current: bool}>
     */
    private static function siblings(User $user, UserRole $role, ?Model $entity): array
    {
        $owned = match ($role) {
            UserRole::Club => $user->ownedClubs,
            UserRole::Shop => $user->shops,
            default => collect(),
        };

        if ($owned->count() < 2) {
            return [];
        }

        return $owned
            ->map(fn (Model $item): array => [
                'label' => $item->name,
                'href' => route($role->dashboardRoute(), $item),
                'current' => $item->is($entity),
            ])
            ->values()
            ->all();
    }

    /**
     * @return array<string, mixed>
     */
    private static function club(User $user, Club $club): array
    {
        $summary = $club->reviewSummary();

        return [
            'entityLabel' => 'باشگاه '.$club->name,
            'publicUrl' => route('clubs.show', $club),
            'editRoute' => route('panel.club.profile', $club),
            'badges' => self::badges($club),
            'stats' => [
                self::stat('اعضای باشگاه', fa_num($club->member_count), 'users', 'blue'),
                self::stat('برنامه‌های فعال', fa_num($club->programs()->active()->count()), 'calendar-days', 'purple', fa_num($club->programs()->count()).' برنامه در مجموع'),
                self::stat('مربیان باشگاه', fa_num($club->coachProfiles()->count()), 'academic-cap', 'green'),
                self::stat('گزارش‌های منتشرشده', fa_num($club->programReports()->count()), 'document-text', 'teal'),
                self::stat('امتیاز کاربران', $summary['average'] ? fa_num($summary['average'], 1) : '—', 'star', 'amber', fa_num($summary['count']).' نظر تأییدشده'),
                self::stat('بازدید صفحه', fa_num($club->view_count), 'eye', 'zinc'),
            ],
            'completeness' => self::completeness([
                'نام باشگاه' => filled($club->name),
                'خلاصه معرفی' => filled($club->summary),
                'توضیحات کامل' => filled($club->description),
                'نشانی' => filled($club->address),
                'موقعیت روی نقشه' => $club->hasCoordinates(),
                'نام مدیر باشگاه' => filled($club->manager),
                'لوگوی باشگاه' => filled($club->logo_path),
            ], route('panel.club.profile', $club)),
            'programsTitle' => 'برنامه‌های باشگاه',
            'programs' => $club->programs()->with('destination:id,name')->orderByDesc('starts_at')->limit(5)->get(),
            'reviewsTitle' => 'نظرات کاربران درباره باشگاه',
            ...self::entityReviews($club),
            'actions' => [
                self::action('ویرایش اطلاعات باشگاه', 'pencil-square', route('panel.club.profile', $club)),
                self::action('مشاهده صفحه عمومی', 'arrow-top-right-on-square', route('clubs.show', $club), true),
                self::action('کاوش مقاصد', 'flag', route('destinations.index'), true),
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function coach(User $user, CoachProfile $profile): array
    {
        $summary = $profile->reviewSummary();
        $editRoute = route(($profile->is_leader ? UserRole::Leader : UserRole::Coach)->profileRoute());

        return [
            'entityLabel' => $profile->headline ?: $profile->roleLabel(),
            'publicUrl' => route('coaches.show', $profile),
            'editRoute' => $editRoute,
            'badges' => self::badges($profile),
            'stats' => [
                self::stat('دوره‌های تدریس‌شده', fa_num($profile->total_courses_taught), 'academic-cap', 'green'),
                self::stat('سابقه فعالیت', $profile->experience_years ? fa_num($profile->experience_years).' سال' : '—', 'clock', 'blue'),
                self::stat('برنامه‌های هدایت‌شده', fa_num($profile->ledPrograms()->count()), 'calendar-days', 'purple', fa_num($profile->ledPrograms()->active()->count()).' برنامه فعال'),
                self::stat('امتیاز کاربران', $summary['average'] ? fa_num($summary['average'], 1) : '—', 'star', 'amber', fa_num($summary['count']).' نظر تأییدشده'),
                self::stat('باشگاه', $profile->club?->name ?? '—', 'building-office-2', 'teal'),
            ],
            'completeness' => self::completeness([
                'عنوان / سمت' => filled($profile->headline),
                'تخصص' => filled($profile->specialty),
                'بیوگرافی' => filled($profile->bio),
                'سال‌های تجربه' => (int) $profile->experience_years > 0,
                'عضویت در باشگاه' => $profile->club_id !== null,
                'تصویر پروفایل' => filled($profile->avatar_path),
            ], $editRoute),
            'programsTitle' => 'برنامه‌های تحت سرپرستی من',
            'programs' => $profile->ledPrograms()->with('destination:id,name')->orderByDesc('starts_at')->limit(5)->get(),
            'reviewsTitle' => 'نظرات کاربران درباره من',
            ...self::entityReviews($profile),
            'actions' => [
                self::action('ویرایش پروفایل تخصصی', 'pencil-square', $editRoute),
                self::action('مشاهده صفحه عمومی', 'arrow-top-right-on-square', route('coaches.show', $profile), true),
                self::action('برنامه‌های سایت', 'calendar-days', route('programs.index'), true),
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function shop(User $user, Shop $shop): array
    {
        $summary = $shop->reviewSummary();

        return [
            'entityLabel' => 'فروشگاه '.$shop->name,
            'publicUrl' => route('shops.show', $shop),
            'editRoute' => route('panel.shop.profile', $shop),
            'badges' => self::badges($shop),
            'stats' => [
                self::stat('امتیاز مشتریان', $summary['average'] ? fa_num($summary['average'], 1) : '—', 'star', 'amber', fa_num($summary['count']).' نظر تأییدشده'),
                self::stat('وضعیت تأیید', $shop->is_verified ? 'تأییدشده' : 'در انتظار تأیید', 'check-badge', $shop->is_verified ? 'green' : 'amber'),
                self::stat('شهر', $shop->city ?: '—', 'map-pin', 'blue'),
                self::stat('موقعیت روی نقشه', $shop->hasCoordinates() ? 'ثبت‌شده' : 'ثبت نشده', 'map', $shop->hasCoordinates() ? 'teal' : 'red'),
            ],
            'completeness' => self::completeness([
                'نام فروشگاه' => filled($shop->name),
                'معرفی کوتاه' => filled($shop->summary),
                'نشانی' => filled($shop->address),
                'موقعیت روی نقشه' => $shop->hasCoordinates(),
                'لوگوی فروشگاه' => filled($shop->logo_path),
            ], route('panel.shop.profile', $shop)),
            'reviewsTitle' => 'نظرات مشتریان',
            ...self::entityReviews($shop),
            'actions' => [
                self::action('ویرایش اطلاعات فروشگاه', 'pencil-square', route('panel.shop.profile', $shop)),
                self::action('مشاهده صفحه عمومی', 'arrow-top-right-on-square', route('shops.show', $shop), true),
                self::action('نقشه کوهنوردی', 'map', route('map'), true),
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function climber(User $user): array
    {
        $reviews = Review::query()->where('user_id', $user->id);

        return [
            'entityLabel' => 'کوهنورد',
            'stats' => [
                self::stat('نظرات من', fa_num((clone $reviews)->count()), 'chat-bubble-left-right', 'blue'),
                self::stat('نظرات تأییدشده', fa_num((clone $reviews)->where('status', ReviewStatus::Approved)->count()), 'check-badge', 'green'),
                self::stat('در انتظار تأیید', fa_num((clone $reviews)->where('status', ReviewStatus::Pending)->count()), 'clock', 'amber'),
                self::stat('برنامه‌های پیش‌رو', fa_num(Program::query()->active()->count()), 'calendar-days', 'purple', 'برای شرکت در سایت', route('programs.index')),
            ],
            'completeness' => self::completeness([
                'نام و موبایل' => true,
                'ایمیل' => filled($user->email),
                'باشگاه اصلی' => $user->primary_club_id !== null,
                'ثبت اولین نظر' => (clone $reviews)->exists(),
            ], route('profile.edit')),
            'reviewsTitle' => 'نظرات اخیر من',
            'reviews' => (clone $reviews)->with('reviewable')->latest()->limit(4)->get(),
            'suggestions' => self::suggestions(),
            'actions' => [
                self::action('جستجوی مقصد', 'flag', route('destinations.index'), true),
                self::action('باشگاه‌های کرمان', 'building-office-2', route('clubs.index'), true),
                self::action('نقشه کوهنوردی', 'map', route('map'), true),
                self::action('تنظیمات حساب', 'cog-6-tooth', route('profile.edit')),
            ],
        ];
    }

    /**
     * @return Collection<int, Program>
     */
    private static function upcomingPrograms(): Collection
    {
        return Program::query()
            ->with(['destination:id,name', 'club:id,name'])
            ->active()
            ->orderBy('starts_at')
            ->limit(5)
            ->get();
    }

    /**
     * @return Collection<int, Destination>
     */
    private static function suggestions(): Collection
    {
        return Destination::query()
            ->where('is_featured', true)
            ->orderByDesc('view_count')
            ->limit(4)
            ->get();
    }

    /**
     * @return array{reviews: Collection<int, Review>, pendingReviews: int}
     */
    private static function entityReviews(Model $entity): array
    {
        return [
            'reviews' => $entity->reviews()->approved()->with('user:id,name')->latest()->limit(4)->get(),
            'pendingReviews' => $entity->reviews()->pending()->count(),
        ];
    }

    /**
     * @return list<array{label: string, color: string}>
     */
    private static function badges(Model $entity): array
    {
        return array_values(array_filter([
            $entity->is_verified ? ['label' => 'تأیید شده', 'color' => 'green'] : ['label' => 'در انتظار تأیید', 'color' => 'amber'],
            $entity->is_featured ? ['label' => 'ویژه', 'color' => 'blue'] : null,
            $entity->is_premium ? ['label' => 'پریمیوم', 'color' => 'purple'] : null,
        ]));
    }

    /**
     * @param  array<string, bool>  $checks
     * @return array{percent: int, items: list<array{label: string, done: bool, href: string}>}
     */
    private static function completeness(array $checks, string $href): array
    {
        $items = [];

        foreach ($checks as $label => $done) {
            $items[] = ['label' => $label, 'done' => (bool) $done, 'href' => $href];
        }

        $done = count(array_filter($items, fn (array $item): bool => $item['done']));

        return [
            'percent' => $items === [] ? 0 : (int) round($done / count($items) * 100),
            'items' => $items,
        ];
    }

    /**
     * @return array{label: string, value: string, icon: string, tone: string, hint: ?string, href: ?string}
     */
    private static function stat(string $label, string $value, string $icon, string $tone, ?string $hint = null, ?string $href = null): array
    {
        return compact('label', 'value', 'icon', 'tone', 'hint', 'href');
    }

    /**
     * @return array{label: string, icon: string, href: string, external: bool}
     */
    private static function action(string $label, string $icon, string $href, bool $external = false): array
    {
        return compact('label', 'icon', 'href', 'external');
    }
}
