<?php

namespace Database\Seeders;

use App\Enums\FundIncomeSource;
use App\Enums\PlanBadgeColor;
use App\Enums\UserRole;
use App\Models\Club;
use App\Models\CoachProfile;
use App\Models\FundIncome;
use App\Models\MembershipDue;
use App\Models\MembershipPlan;
use App\Models\Shop;
use App\Models\User;
use App\Support\JalaliDate;
use Illuminate\Database\Seeder;

/**
 * Seeds membership plans for every group plus sample members in different payment states,
 * so plan choice, level badges and the "supporters" list can be reviewed. Safe to re-run.
 */
class MembershipPlanSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedPlans();
        $this->seedSampleMembers();
    }

    private function seedPlans(): void
    {
        /** @var array<string, array{terms: array{0: bool, 1: bool, 2: bool}, discounts: array{0: int, 1: int}}> $terms */
        $terms = [
            'climber' => ['terms' => [false, false, true], 'discounts' => [0, 0]],
            'club' => ['terms' => [false, true, true], 'discounts' => [5, 12]],
            'coach' => ['terms' => [false, true, true], 'discounts' => [5, 10]],
            'leader' => ['terms' => [false, true, true], 'discounts' => [5, 10]],
            'shop' => ['terms' => [true, true, true], 'discounts' => [8, 15]],
        ];

        $plans = [
            [UserRole::Climber, 'کوهنورد عضو', 1, 300_000, PlanBadgeColor::Zinc, 'star', 'عضو', 'شروع همراهی با انجمن.', ['مشاهده گزارش مالی صندوق', 'دریافت خبرنامه برنامه‌ها']],
            [UserRole::Climber, 'کوهنورد حامی', 2, 1_000_000, PlanBadgeColor::Sky, 'shield-check', 'حامی', 'حمایت فعال‌تر از توسعه گردشگری.', ['همه امکانات سطح ۱', 'نشان سطح برای اعضای واردشده', 'اولویت ثبت‌نام در برنامه‌های محدود']],
            [UserRole::Climber, 'کوهنورد پیشگام', 3, 3_000_000, PlanBadgeColor::Amber, 'trophy', 'پیشگام', 'برای کسانی که در مربی‌پروری سهیم می‌شوند.', ['همه امکانات سطح ۲', 'نام در فهرست حامیان صندوق', 'دعوت به نشست‌های سالانه']],

            [UserRole::Club, 'باشگاه عضو', 1, 3_000_000, PlanBadgeColor::Zinc, 'flag', 'باشگاه عضو', 'عضویت پایه باشگاه‌ها در انجمن.', ['حضور در دایرکتوری باشگاه‌ها', 'ثبت و معرفی برنامه‌ها', 'حق رأی در مجمع']],
            [UserRole::Club, 'باشگاه حامی', 2, 8_000_000, PlanBadgeColor::Violet, 'sparkles', 'باشگاه حامی', 'برای باشگاه‌هایی که در توسعه صندوق نقش دارند.', ['همه امکانات سطح ۱', 'نشان «باشگاه حامی»', 'معرفی برنامه‌ها در تقویم انجمن', 'اولویت در دوره‌های مربی‌پروری']],
            [UserRole::Club, 'باشگاه شریک راهبردی', 3, 15_000_000, PlanBadgeColor::Amber, 'trophy', 'شریک راهبردی', 'بالاترین سطح همکاری باشگاه‌ها.', ['همه امکانات سطح ۲', 'نشان «شریک راهبردی»', 'نماینده در شورای برنامه‌ریزی', 'معرفی ویژه در رویدادهای انجمن']],

            [UserRole::Coach, 'مربی عضو', 1, 500_000, PlanBadgeColor::Zinc, 'star', 'مربی عضو', 'عضویت پایه مربیان.', ['پروفایل تخصصی در دایرکتوری', 'دسترسی به منابع آموزشی']],
            [UserRole::Coach, 'مربی همراه', 2, 1_500_000, PlanBadgeColor::Emerald, 'shield-check', 'مربی همراه', 'برای مربیانی که از آموزش مربیان جدید حمایت می‌کنند.', ['همه امکانات سطح ۱', 'نشان «مربی همراه»', 'اولویت در همکاری با برنامه‌های انجمن']],
            [UserRole::Coach, 'مربی پیشرو', 3, 3_000_000, PlanBadgeColor::Amber, 'trophy', 'مربی پیشرو', 'بالاترین سطح حمایت مربیان.', ['همه امکانات سطح ۲', 'نشان «مربی پیشرو»', 'عضویت در تیم آموزش مربیان']],

            [UserRole::Leader, 'سرپرست عضو', 1, 500_000, PlanBadgeColor::Zinc, 'flag', 'سرپرست عضو', 'عضویت پایه سرپرستان برنامه.', ['پروفایل در دایرکتوری سرپرستان', 'ثبت گزارش برنامه‌ها']],
            [UserRole::Leader, 'سرپرست برجسته', 2, 2_000_000, PlanBadgeColor::Rose, 'fire', 'سرپرست برجسته', 'حمایت ویژه سرپرستان از صندوق.', ['همه امکانات سطح ۱', 'نشان «سرپرست برجسته»', 'معرفی ویژه در گزارش‌های برنامه']],
            [UserRole::Leader, 'سرپرست ارشد', 3, 3_500_000, PlanBadgeColor::Amber, 'trophy', 'سرپرست ارشد', 'برای سرپرستان پیشگام انجمن.', ['همه امکانات سطح ۲', 'نشان «سرپرست ارشد»', 'اولویت در برنامه‌های مهم']],

            [UserRole::Shop, 'فروشگاه همکار', 1, 2_000_000, PlanBadgeColor::Zinc, 'star', 'فروشگاه همکار', 'عضویت پایه فروشگاه‌ها.', ['حضور در دایرکتوری فروشگاه‌ها', 'ثبت اطلاعات و موقعیت روی نقشه']],
            [UserRole::Shop, 'فروشگاه حامی', 2, 5_000_000, PlanBadgeColor::Sky, 'heart', 'فروشگاه حامی', 'حمایت فروشگاه‌ها از برنامه‌ها و مربی‌پروری.', ['همه امکانات سطح ۱', 'نشان «فروشگاه حامی»', 'لوگو در صفحه حامیان']],
            [UserRole::Shop, 'فروشگاه شریک طلایی', 3, 10_000_000, PlanBadgeColor::Amber, 'trophy', 'شریک طلایی', 'بالاترین سطح همکاری فروشگاه‌ها.', ['همه امکانات سطح ۲', 'نشان «شریک طلایی»', 'اسپانسری یک رویداد در سال']],

        ];

        foreach ($plans as [$role, $name, $level, $price, $color, $icon, $badge, $description, $features]) {
            $config = $terms[$role->value];

            MembershipPlan::query()->updateOrCreate(
                ['name' => $name],
                [
                    'role' => $role,
                    'level' => $level,
                    'price' => $price,
                    'duration_days' => 365,
                    'allow_monthly' => $config['terms'][0],
                    'allow_semiannual' => $config['terms'][1],
                    'allow_annual' => $config['terms'][2],
                    'discount_semiannual' => $config['discounts'][0],
                    'discount_annual' => $config['discounts'][1],
                    'badge_color' => $color,
                    'badge_icon' => $icon,
                    'badge_label' => $badge,
                    'is_active' => true,
                    'description' => $description,
                    'features' => $features,
                ],
            );
        }

        MembershipPlan::query()->updateOrCreate(
            ['name' => 'پلن قدیمی باشگاه (غیرفعال)'],
            ['role' => UserRole::Club, 'level' => 1, 'price' => 1_000_000, 'is_active' => false, 'badge_color' => PlanBadgeColor::Zinc, 'badge_icon' => 'star', 'description' => 'نمونه‌ی پلن غیرفعال؛ برای اعضا نمایش داده نمی‌شود.', 'features' => ['ویژگی قدیمی']],
        );
    }

    /**
     * Sample members covering the states worth reviewing: settled (badge shown), partial,
     * overdue, expired, a custom amount without plan, and a member who has not chosen anything.
     */
    /**
     * Roles come from what the account owns, so each sample member gets a minimal club, shop or coach profile.
     */
    private function ensureRoleProfile(User $user, UserRole $role): void
    {
        $slug = 'sample-'.$user->username;

        if ($role === UserRole::Club && ! $user->ownedClubs()->exists()) {
            Club::factory()->create(['owner_id' => $user->id, 'slug' => $slug]);
        }

        if ($role === UserRole::Shop && ! $user->shops()->exists()) {
            Shop::factory()->create(['user_id' => $user->id, 'slug' => $slug]);
        }

        if (in_array($role, [UserRole::Coach, UserRole::Leader], true) && ! $user->coachProfile()->exists()) {
            CoachProfile::factory()->create(['user_id' => $user->id, 'slug' => $slug, 'is_leader' => $role === UserRole::Leader]);
        }
    }

    private function seedSampleMembers(): void
    {
        $period = (string) JalaliDate::currentYear();

        // [mobile, username, name, role, plan, paid share, term months, started days ago]
        $members = [
            ['09120000101', 'sample-coach-silver', 'مربی نمونه (تسویه‌شده)', UserRole::Coach, 'مربی همراه', 1.0, 12, 30],
            ['09120000102', 'sample-leader-partial', 'سرپرست نمونه (پرداخت ناقص)', UserRole::Leader, 'سرپرست برجسته', 0.4, 6, 5],
            ['09120000103', 'sample-shop-gold', 'فروشگاه نمونه طلایی (شش‌ماهه)', UserRole::Shop, 'فروشگاه شریک طلایی', 1.0, 6, 20],
            ['09120000104', 'sample-club-overdue', 'باشگاه نمونه (معوق)', UserRole::Club, 'باشگاه حامی', 0.0, 12, 45],
            ['09120000106', 'sample-climber-gold', 'کوهنورد پیشگام', UserRole::Climber, 'کوهنورد پیشگام', 1.0, 12, 90],
            ['09120000107', 'sample-climber-basic', 'کوهنورد عضو (قابل ارتقا)', UserRole::Climber, 'کوهنورد عضو', 1.0, 12, 100],
            ['09120000108', 'sample-climber-expired', 'کوهنورد با عضویت منقضی', UserRole::Climber, 'کوهنورد حامی', 1.0, 12, 400],
            ['09120000109', 'sample-club-level3', 'باشگاه نمونه سطح سه', UserRole::Club, 'باشگاه شریک راهبردی', 1.0, 12, 15],
            ['09120000110', 'sample-coach-custom', 'مربی با مبلغ سفارشی', UserRole::Coach, null, 1.0, 12, 10],
            ['09120000111', 'sample-shop-nochoice', 'فروشگاه بدون انتخاب پلن', UserRole::Shop, false, 0.0, 12, 0],
        ];

        foreach ($members as [$mobile, $username, $name, $role, $planName, $paidShare, $months, $startedDaysAgo]) {
            $user = User::query()->updateOrCreate(
                ['mobile' => $mobile],
                ['name' => $name, 'username' => $username, 'password' => 'password'],
            );

            $this->ensureRoleProfile($user, $role);

            $user->membershipDues()->delete();

            if ($planName === false) {
                continue;
            }

            $plan = $planName ? MembershipPlan::query()->where('name', $planName)->first() : null;
            $term = $plan?->termPrice($months);
            $startsOn = now()->subDays($startedDaysAgo)->startOfDay();

            $due = MembershipDue::query()->create([
                'user_id' => $user->id,
                'membership_plan_id' => $plan?->id,
                'period' => $period,
                'title' => 'حق عضویت '.$period.' — '.$name,
                'amount' => $term['final'] ?? 750_000,
                'term_months' => $months,
                'discount_percent' => $term['discount'] ?? 0,
                'starts_on' => $startsOn->toDateString(),
                'ends_on' => $startsOn->copy()->addDays(MembershipPlan::TERM_DAYS[$months])->toDateString(),
                'due_date' => $paidShare === 0.0 ? now()->subMonth()->toDateString() : now()->addMonths(3)->toDateString(),
            ]);

            if ($paidShare > 0) {
                FundIncome::query()->create([
                    'source' => FundIncomeSource::Membership,
                    'amount' => (int) round($due->amount * $paidShare),
                    'received_at' => $startsOn->toDateString(),
                    'user_id' => $user->id,
                    'membership_due_id' => $due->id,
                ]);
            }
        }
    }
}
