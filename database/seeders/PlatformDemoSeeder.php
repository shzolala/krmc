<?php

namespace Database\Seeders;

use App\Enums\FundExpenseCategory;
use App\Enums\FundIncomeSource;
use App\Enums\LodgingType;
use App\Enums\ProgramStatus;
use App\Enums\UserRole;
use App\Models\Accommodation;
use App\Models\Club;
use App\Models\CoachProfile;
use App\Models\Destination;
use App\Models\DestinationGuide;
use App\Models\FinanceLedgerAccess;
use App\Models\FundCampaign;
use App\Models\FundExpense;
use App\Models\FundIncome;
use App\Models\MembershipDue;
use App\Models\MembershipPlan;
use App\Models\Program;
use App\Models\ProgramReport;
use App\Models\Shelter;
use App\Models\Shop;
use App\Models\User;
use App\Support\CatalogCover;
use App\Support\JalaliDate;
use App\Support\PanelRegistry;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PlatformDemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::factory()->admin()->create([
            'name' => 'مدیر سامانه',
            'mobile' => '09120000001',
            'username' => 'admin',
        ]);

        $clubOwner = User::factory()->create([
            'name' => 'مدیر باشگاه نمونه',
            'mobile' => '09120000002',
            'username' => 'club-kerman',
        ]);

        $shopOwner = User::factory()->create([
            'name' => 'مدیر فروشگاه نمونه',
            'mobile' => '09120000003',
            'username' => 'shop-kouhestan',
        ]);

        $this->call([
            KermanGeographySeeder::class,
            KermanFarakouhSeeder::class,
        ]);

        $clubs = Club::query()->orderBy('id')->get();
        $primaryClub = $clubs->firstWhere('farakouh_id', 381)
            ?? $clubs->firstWhere('slug', 'club-381')
            ?? $clubs->first();

        if ($primaryClub !== null) {
            $primaryClub->update([
                'owner_id' => $clubOwner->id,
                'is_featured' => true,
                'is_premium' => true,
            ]);

            $clubOwner->update(['primary_club_id' => $primaryClub->id]);
            $primaryClub->members()->syncWithoutDetaching([
                $clubOwner->id => ['status' => 'active'],
            ]);
        }

        $destinations = Destination::query()
            ->peaks()
            ->orderByDesc('elevation')
            ->limit(12)
            ->get();

        if ($destinations->isEmpty()) {
            $destinations = Destination::query()->orderByDesc('is_featured')->limit(12)->get();
        }

        $coachPeople = [
            ['name' => 'علی رضایی', 'slug' => 'ali-rezaei', 'mobile' => '09120000005', 'leader' => true, 'specialty' => 'کوهنوردی فنی'],
            ['name' => 'سارا محمدی', 'slug' => 'sara-mohammadi', 'mobile' => '09120000006', 'leader' => false, 'specialty' => 'صخره‌نوردی'],
            ['name' => 'حسین اکبری', 'slug' => 'hossein-akbari', 'mobile' => '09120000007', 'leader' => true, 'specialty' => 'سرپرستی برنامه'],
            ['name' => 'مریم حسینی', 'slug' => 'maryam-hosseini', 'mobile' => '09120000008', 'leader' => false, 'specialty' => 'یخ‌نوردی'],
        ];

        $coaches = collect($coachPeople)->values()->map(function (array $person, int $index) use ($clubs) {
            $club = $clubs[$index % max(1, $clubs->count())];

            $user = User::factory()->create([
                'name' => $person['name'],
                'mobile' => $person['mobile'],
                'username' => $person['slug'],
                'primary_club_id' => $club->id,
            ]);

            $club->members()->syncWithoutDetaching([
                $user->id => ['status' => 'active'],
            ]);

            return CoachProfile::query()->create([
                'user_id' => $user->id,
                'club_id' => $club->id,
                'slug' => $person['slug'],
                'headline' => $person['specialty'].' · فعال در باشگاه‌های کرمان',
                'bio' => 'پروفایل عمومی '.$person['name'].' برای معرفی تخصص، برنامه‌ها و مقاصدی که هدایت می‌کند.',
                'specialty' => $person['specialty'],
                'experience_years' => random_int(5, 20),
                'is_verified' => true,
                'is_featured' => true,
                'is_premium' => $index < 2,
                'is_leader' => $person['leader'],
            ]);
        });

        $realCoaches = CoachProfile::query()
            ->whereNotNull('farakouh_id')
            ->orderByDesc('total_courses_taught')
            ->limit(8)
            ->get();

        foreach ($destinations as $index => $destination) {
            DestinationGuide::query()->create([
                'destination_id' => $destination->id,
                'club_id' => $clubs[$index % max(1, $clubs->count())]->id,
                'coach_profile_id' => null,
                'guide_type' => 'club',
            ]);

            $coach = $realCoaches[$index % max(1, $realCoaches->count())] ?? $coaches[$index % $coaches->count()];

            DestinationGuide::query()->create([
                'destination_id' => $destination->id,
                'club_id' => null,
                'coach_profile_id' => $coach->id,
                'guide_type' => 'coach',
            ]);
        }

        foreach ($clubs->take(3) as $club) {
            ProgramReport::query()->create([
                'title' => 'گزارش صعود '.$destinations->random()->name.' با '.$club->name,
                'slug' => Str::slug('report-'.$club->slug.'-'.Str::random(5)),
                'excerpt' => 'گزارش کامل برنامه برای دیده‌شدن در گوگل و اطلاع‌رسانی به جامعه کوهنوردی کرمان.',
                'body' => "این گزارش نمونه محتوای سئو‌محور است.\nدر آینده به ثبت‌نام دوره و برنامه باشگاهی متصل می‌شود.",
                'cover_path' => CatalogCover::path('cover-04.jpg'),
                'destination_id' => $destinations->random()->id,
                'club_id' => $club->id,
                'author_id' => $admin->id,
                'published_at' => now()->subDays(random_int(2, 40)),
            ]);
        }

        $leaders = $coaches->where('is_leader', true)->values();
        $programBlueprints = [
            [
                'title' => 'صعود یک‌روزه هزار',
                'slug' => 'program-hezar-day',
                'status' => ProgramStatus::Ongoing,
                'starts_at' => now()->subDay(),
                'ends_at' => now()->addDays(1),
                'with_leader' => true,
            ],
            [
                'title' => 'برنامه فنی جوپار',
                'slug' => 'program-joopar-tech',
                'status' => ProgramStatus::Ongoing,
                'starts_at' => now()->subDays(2),
                'ends_at' => now()->addDays(2),
                'with_leader' => true,
            ],
            [
                'title' => 'طبیعت‌گردی لاله‌زار',
                'slug' => 'program-lalezar-nature',
                'status' => ProgramStatus::Upcoming,
                'starts_at' => now()->addDays(10),
                'ends_at' => now()->addDays(12),
                'with_leader' => false,
            ],
            [
                'title' => 'صعود آموزشی اماوند',
                'slug' => 'program-amavand-training',
                'status' => ProgramStatus::Upcoming,
                'starts_at' => now()->addDays(18),
                'ends_at' => now()->addDays(19),
                'with_leader' => true,
            ],
        ];

        foreach ($programBlueprints as $index => $blueprint) {
            $destination = $destinations[$index % max(1, $destinations->count())];
            $club = $clubs[$index % max(1, $clubs->count())];
            $leader = $blueprint['with_leader']
                ? ($leaders[$index % max(1, $leaders->count())] ?? null)
                : null;

            Program::query()->create([
                'title' => $blueprint['title'],
                'slug' => $blueprint['slug'],
                'summary' => 'برنامه باشگاهی برای '.$destination->name.' با هماهنگی '.$club->name.'.',
                'description' => "این برنامه برای مقصد «{$destination->name}» تعریف شده است.\nباشگاه برگزارکننده الزامی است و سرپرست در صورت نیاز مشخص می‌شود.",
                'status' => $blueprint['status'],
                'destination_id' => $destination->id,
                'club_id' => $club->id,
                'leader_id' => $leader?->id,
                'starts_at' => $blueprint['starts_at'],
                'ends_at' => $blueprint['ends_at'],
                'capacity' => 20 + ($index * 4),
                'registered_count' => 4 + $index,
                'cover_path' => CatalogCover::for('program-'.$blueprint['slug']),
                'is_featured' => $index < 2,
            ]);
        }

        Shop::query()->create([
            'user_id' => $shopOwner->id,
            'name' => 'فروشگاه کوهستان کرمان',
            'slug' => 'kouhestan-kerman',
            'summary' => 'تجهیزات کوهنوردی، پوشاک فنی و لوازم برنامه‌های باشگاهی.',
            'city' => 'کرمان',
            'latitude' => 30.2839000,
            'longitude' => 57.0834000,
            'address' => 'کرمان، خیابان شریعتی',
            'is_verified' => true,
            'is_featured' => true,
            'is_premium' => true,
        ]);

        Shop::query()->create([
            'user_id' => null,
            'name' => 'آریا اسپرت',
            'slug' => 'arya-sport',
            'summary' => 'عرضه‌کننده کفش، کوله و ابزار سنگ‌نوردی.',
            'city' => 'کرمان',
            'latitude' => 30.2950000,
            'longitude' => 57.0700000,
            'address' => 'کرمان، بلوار جمهوری',
            'is_verified' => true,
            'is_featured' => false,
            'is_premium' => false,
        ]);

        $hezar = Destination::query()->where('catalog_id', 'peak-hezar')->first()
            ?? $destinations->firstWhere('slug', 'mount-hezar');
        $joopar = Destination::query()->where('catalog_id', 'peak-seh-shakh-bozorg')->first()
            ?? $destinations->skip(1)->first();
        $lalezar = Destination::query()->where('catalog_id', 'peak-lalezar')->first()
            ?? $destinations->skip(2)->first();

        Accommodation::query()->create([
            'name' => 'بوم‌گردی دامنه هزار',
            'slug' => 'eco-hezar',
            'type' => LodgingType::EcoLodge,
            'summary' => 'خانه بوم‌گردی نزدیک مسیر صعود هزار برای شب‌مانی قبل از برنامه.',
            'description' => 'اقامتگاه بوم‌گردی با موقعیت جغرافیایی ثبت‌شده؛ مناسب تیم‌های باشگاهی.',
            'city' => 'راین',
            'cover_path' => CatalogCover::path('cover-lodge.jpg'),
            'latitude' => 29.4800000,
            'longitude' => 57.3000000,
            'address' => 'نزدیکی راین، مسیر هزار',
            'destination_id' => $hezar?->id,
            'is_verified' => true,
            'is_featured' => true,
            'is_premium' => true,
        ]);

        Accommodation::query()->create([
            'name' => 'هتل کوهستان کرمان',
            'slug' => 'hotel-kouhestan',
            'type' => LodgingType::Hotel,
            'summary' => 'هتل شهری برای استراحت قبل و بعد از برنامه‌های کوهنوردی.',
            'description' => 'اقامتگاه هتلی در کرمان با دسترسی مناسب به باشگاه‌ها و فروشگاه‌ها.',
            'city' => 'کرمان',
            'cover_path' => CatalogCover::path('cover-07.jpg'),
            'latitude' => 30.2880000,
            'longitude' => 57.0760000,
            'address' => 'کرمان، مرکز شهر',
            'destination_id' => null,
            'is_verified' => true,
            'is_featured' => true,
            'is_premium' => false,
        ]);

        Accommodation::query()->create([
            'name' => 'مهمان‌سرای لاله‌زار',
            'slug' => 'guesthouse-lalezar',
            'type' => LodgingType::Guesthouse,
            'summary' => 'اقامت ساده نزدیک مسیر لاله‌زار.',
            'description' => 'مناسب برنامه‌های چندروزه باشگاهی.',
            'city' => 'لاله‌زار',
            'cover_path' => CatalogCover::path('cover-11.jpg'),
            'latitude' => 29.4000000,
            'longitude' => 57.0000000,
            'address' => 'منطقه لاله‌زار',
            'destination_id' => $lalezar?->id,
            'is_verified' => true,
            'is_featured' => false,
            'is_premium' => false,
        ]);

        Accommodation::query()->create([
            'name' => 'کمپ پایه جوپار',
            'slug' => 'camp-joopar',
            'type' => LodgingType::Camp,
            'summary' => 'کمپ اقامتی موقت برای برنامه‌های جوپار (جدا از پناهگاه کوهستانی).',
            'description' => 'این کمپ یک اقامتگاه موقت است و با پناهگاه جوپار متفاوت است.',
            'city' => 'کرمان',
            'cover_path' => CatalogCover::path('cover-camp.jpg'),
            'latitude' => 29.9300000,
            'longitude' => 57.1600000,
            'address' => 'پای مسیر جوپار',
            'destination_id' => $joopar?->id,
            'is_verified' => true,
            'is_featured' => true,
            'is_premium' => false,
        ]);

        Shelter::query()->create([
            'name' => 'پناهگاه هزار',
            'slug' => 'shelter-hezar',
            'summary' => 'پناهگاه کوهستانی مسیر هزار — نقطه حمایتی صعود، نه اقامتگاه تجاری.',
            'description' => "پناهگاه هزار بخشی از زیرساخت کوهستانی کرمان است.\nبرخلاف بوم‌گردی و هتل، اینجا برای پشتیبانی برنامه و شرایط اضطراری مسیر استفاده می‌شود.",
            'elevation' => 3200,
            'capacity' => 30,
            'cover_path' => CatalogCover::path('cover-17.jpg'),
            'latitude' => 29.5050000,
            'longitude' => 57.2750000,
            'address' => 'مسیر صعود هزار',
            'destination_id' => $hezar?->id,
            'is_featured' => true,
        ]);

        Shelter::query()->create([
            'name' => 'پناهگاه جوپار',
            'slug' => 'shelter-joopar',
            'summary' => 'پناهگاه کوهستانی جوپار — ویژه و متمایز از اقامتگاه‌های بوم‌گردی و هتل.',
            'description' => "پناهگاه جوپار برای شب‌مانی فنی و پشتیبانی تیم‌های کوهنوردی است.\nدر پلتفرم به‌صورت بخش ویژه پناهگاه‌ها نمایش داده می‌شود.",
            'elevation' => 2900,
            'capacity' => 25,
            'cover_path' => CatalogCover::path('cover-09.jpg'),
            'latitude' => 29.9050000,
            'longitude' => 57.1750000,
            'address' => 'مسیر صعود جوپار',
            'destination_id' => $joopar?->id,
            'is_featured' => true,
        ]);

        User::factory()->create([
            'name' => 'کوهنورد نمونه',
            'mobile' => '09120000010',
            'username' => 'climber',
            'primary_club_id' => $primaryClub?->id ?? $clubs->first()?->id,
        ]);

        $this->seedMultiRoleMember($primaryClub);

        $this->seedFund($clubOwner, $shopOwner);
        $this->call(ClimbBadgeSeeder::class);
    }

    /**
     * One person who runs a club, a shop and teaches as a coach, all behind a single mobile number.
     */
    private function seedMultiRoleMember(?Club $primaryClub): void
    {
        $member = User::factory()->create([
            'name' => 'رضا کرمانی (باشگاه، فروشگاه و مربی)',
            'mobile' => PanelRegistry::MULTI_ROLE_DEMO_MOBILE,
            'username' => 'multi-role',
            'primary_club_id' => $primaryClub?->id,
        ]);

        Club::factory()->create([
            'owner_id' => $member->id,
            'name' => 'باشگاه کوهنوردی رضا',
            'slug' => 'reza-mountaineering-club',
            'is_verified' => true,
        ]);

        Shop::query()->create([
            'user_id' => $member->id,
            'name' => 'فروشگاه تجهیزات رضا',
            'slug' => 'reza-gear',
            'summary' => 'تجهیزات کوهنوردی و لوازم اردو.',
            'city' => 'کرمان',
            'address' => 'کرمان، خیابان مطهری',
            'is_verified' => true,
        ]);

        CoachProfile::query()->create([
            'user_id' => $member->id,
            'club_id' => $primaryClub?->id,
            'slug' => 'reza-kermani',
            'headline' => 'مربی کوهنوردی · مدیر باشگاه و فروشگاه',
            'bio' => 'نمونه‌ای از یک حساب با چند نقش: مدیر باشگاه، صاحب فروشگاه و مربی در یک پنل.',
            'specialty' => 'کوهنوردی فنی',
            'experience_years' => 12,
            'is_verified' => true,
            'is_leader' => false,
        ]);
    }

    private function seedFund(User $clubOwner, User $shopOwner): void
    {
        $campaign = FundCampaign::factory()->create([
            'title' => 'کمپین مربی‌پروری کرمان',
            'description' => 'تأمین هزینه دوره‌های مربیگری و ارتقای مربیان جوان استان.',
            'goal_amount' => 100_000_000,
        ]);

        $this->call([MembershipPlanSeeder::class, ProjectSeeder::class, HeroSlideSeeder::class]);

        $period = (string) JalaliDate::currentYear();

        foreach ([[$clubOwner, 'باشگاه حامی', 1.0], [$shopOwner, 'فروشگاه همکار', 0.4]] as [$member, $planName, $share]) {
            $plan = MembershipPlan::query()->where('name', $planName)->firstOrFail();
            $paid = (int) round($plan->termPrice(12)['final'] * $share);
            $term = $plan->termPrice(12);
            $due = MembershipDue::factory()->for($member)->create([
                'term_months' => 12,
                'discount_percent' => $term['discount'],
                'starts_on' => now()->subDays(20)->toDateString(),
                'ends_on' => now()->addDays(345)->toDateString(),
                'period' => $period,
                'title' => 'حق عضویت '.$period.' — '.$member->name,
                'membership_plan_id' => $plan->id,
                'amount' => $term['final'],
            ]);

            FundIncome::factory()->create([
                'source' => FundIncomeSource::Membership,
                'amount' => $paid,
                'user_id' => $member->id,
                'membership_due_id' => $due->id,
                'payer_name' => null,
            ]);
        }

        FundIncome::factory()->create(['source' => FundIncomeSource::Sponsorship, 'amount' => 20_000_000, 'payer_name' => 'اسپانسر نمونه', 'fund_campaign_id' => $campaign->id]);

        FundExpense::factory()->create(['title' => 'کارگاه مربیگری درجه ۳', 'category' => FundExpenseCategory::CoachTraining, 'amount' => 8_000_000, 'fund_campaign_id' => $campaign->id]);
        FundExpense::factory()->create(['title' => 'نقشه و تابلوهای مسیر جوپار', 'category' => FundExpenseCategory::TourismDevelopment, 'amount' => 4_000_000]);

        FinanceLedgerAccess::factory()->create(['role' => UserRole::Club, 'note' => 'باشگاه‌ها ریز مالی را می‌بینند']);
    }
}
