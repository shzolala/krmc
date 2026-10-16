<?php

namespace Database\Seeders;

use App\Enums\LodgingType;
use App\Enums\UserRole;
use App\Models\Accommodation;
use App\Models\Club;
use App\Models\CoachProfile;
use App\Models\Destination;
use App\Models\DestinationGuide;
use App\Models\ProgramReport;
use App\Models\Shelter;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PlatformDemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::factory()->create([
            'name' => 'مدیر سامانه',
            'mobile' => '09120000001',
            'username' => 'admin',
            'role' => UserRole::Admin,
        ]);

        $clubOwner = User::factory()->create([
            'name' => 'مدیر باشگاه نمونه',
            'mobile' => '09120000002',
            'username' => 'club-kerman',
            'role' => UserRole::Club,
        ]);

        $shopOwner = User::factory()->create([
            'name' => 'مدیر فروشگاه نمونه',
            'mobile' => '09120000003',
            'username' => 'shop-kouhestan',
            'role' => UserRole::Shop,
        ]);

        User::factory()->create([
            'name' => 'نماینده ارگان',
            'mobile' => '09120000004',
            'username' => 'organization',
            'role' => UserRole::Organization,
        ]);

        $destinations = collect([
            [
                'name' => 'هزار',
                'slug' => 'hezar',
                'elevation' => 4501,
                'latitude' => 29.5167000,
                'longitude' => 57.2833000,
                'summary' => 'بلندترین قله استان کرمان؛ مسیر کلاسیک جامعه کوهنوردی جنوب شرق.',
                'cover_path' => 'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?auto=format&fit=crop&w=1200&q=80',
            ],
            [
                'name' => 'لاله‌زار',
                'slug' => 'lalezar',
                'elevation' => 4374,
                'latitude' => 29.4167000,
                'longitude' => 56.9833000,
                'summary' => 'قله‌ای با چشم‌انداز وسیع و برنامه‌های زمستانه و تابستانه.',
                'cover_path' => 'https://images.unsplash.com/photo-1486870591958-9b9d0d1dda99?auto=format&fit=crop&w=1200&q=80',
            ],
            [
                'name' => 'جوپار',
                'slug' => 'joopar',
                'elevation' => 4135,
                'latitude' => 29.9167000,
                'longitude' => 57.1833000,
                'summary' => 'مسیر محبوب باشگاه‌های کرمان برای تمرین فنی و صعود گروهی.',
                'cover_path' => 'https://images.unsplash.com/photo-1519904981063-b0cf448d479e?auto=format&fit=crop&w=1200&q=80',
            ],
            [
                'name' => 'پلوار',
                'slug' => 'palvar',
                'elevation' => 4233,
                'latitude' => 30.0500000,
                'longitude' => 57.4500000,
                'summary' => 'مقصد تخصصی برای برنامه‌های چندروزه و آموزش سرپرستی.',
                'cover_path' => 'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?auto=format&fit=crop&w=1200&q=80',
            ],
            [
                'name' => 'شاهوار',
                'slug' => 'shahvar',
                'elevation' => 4000,
                'latitude' => 30.3500000,
                'longitude' => 57.1500000,
                'summary' => 'مسیر متنوع برای طبیعت‌گردی و کوهپیمایی سبک تا نیمه‌سنگین.',
                'cover_path' => 'https://images.unsplash.com/photo-1454496522488-7a8e488e8606?auto=format&fit=crop&w=1200&q=80',
            ],
            [
                'name' => 'چال‌میجان',
                'slug' => 'chal-mijan',
                'elevation' => 3200,
                'latitude' => 30.2000000,
                'longitude' => 57.2500000,
                'summary' => 'کمپ و مسیر تمرینی نزدیک کرمان برای دوره‌های باشگاهی.',
                'cover_path' => 'https://images.unsplash.com/photo-1469474968028-56623f02e42e?auto=format&fit=crop&w=1200&q=80',
            ],
            [
                'name' => 'تفتان',
                'slug' => 'taftan',
                'elevation' => 3941,
                'latitude' => 28.6000000,
                'longitude' => 61.1333000,
                'summary' => 'برنامه‌های بین‌استانی باشگاه‌های کرمان به قله آتشفشانی تفتان.',
                'cover_path' => 'https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=1200&q=80',
            ],
            [
                'name' => 'شیرکوه',
                'slug' => 'shirkuh',
                'elevation' => 4075,
                'latitude' => 31.6167000,
                'longitude' => 54.0667000,
                'summary' => 'مقصد سفرهای باشگاهی خارج از استان با گزارش‌های سئو‌محور.',
                'cover_path' => 'https://images.unsplash.com/photo-1483728642387-6c787bd7e83f?auto=format&fit=crop&w=1200&q=80',
            ],
        ])->map(fn (array $data) => Destination::query()->create([
            ...$data,
            'region' => 'کرمان و اطراف',
            'address' => 'موقعیت تقریبی قله '.$data['name'],
            'description' => $data['summary']."\n\nدر صفحه مقصد مشخص می‌شود کدام باشگاه، مربی یا سرپرست این مسیر را برگزار می‌کند.",
            'is_featured' => true,
            'view_count' => random_int(400, 6000),
        ]));

        $clubDefinitions = [
            ['name' => 'باشگاه کوهنوردی کرمان', 'slug' => 'kerman-club', 'premium' => true, 'featured' => true, 'latitude' => 30.2910000, 'longitude' => 57.0780000],
            ['name' => 'باشگاه آذرخش', 'slug' => 'azarakhsh', 'premium' => true, 'featured' => true, 'latitude' => 30.2750000, 'longitude' => 57.0900000],
            ['name' => 'باشگاه دماوند کرمان', 'slug' => 'damavand-kerman', 'premium' => false, 'featured' => true, 'latitude' => 30.3000000, 'longitude' => 57.0600000],
            ['name' => 'باشگاه سنگ‌نوردی کویر', 'slug' => 'kavir-climbing', 'premium' => false, 'featured' => false, 'latitude' => 30.2600000, 'longitude' => 57.1000000],
        ];

        $clubs = collect($clubDefinitions)->map(function (array $data, int $index) use ($clubOwner, $admin) {
            $club = Club::query()->create([
                'name' => $data['name'],
                'slug' => $data['slug'],
                'summary' => 'برگزاری برنامه‌های کوهنوردی، دوره‌های آموزشی و تورهای تخصصی در استان کرمان.',
                'description' => "پروفایل باشگاه در پلتفرم KRMC.\nتعداد اعضا، مربیان، برنامه‌ها و میزان دیده‌شدن بر اساس پلن عضویت و تبلیغات شفاف مدیریت می‌شود.",
                'city' => 'کرمان',
                'latitude' => $data['latitude'],
                'longitude' => $data['longitude'],
                'address' => 'کرمان، محل باشگاه '.$data['name'],
                'founded_year' => random_int(1378, 1401),
                'is_verified' => true,
                'is_featured' => $data['featured'],
                'is_premium' => $data['premium'],
                'member_count' => random_int(80, 360),
                'program_count' => random_int(8, 40),
                'view_count' => random_int(500, 9000),
                'owner_id' => $index === 0 ? $clubOwner->id : $admin->id,
            ]);

            if ($index === 0) {
                $clubOwner->update(['primary_club_id' => $club->id]);
                $club->members()->attach($clubOwner->id, ['status' => 'active']);
            }

            return $club;
        });

        $coachPeople = [
            ['name' => 'علی رضایی', 'slug' => 'ali-rezaei', 'mobile' => '09120000005', 'leader' => true, 'specialty' => 'کوهنوردی فنی'],
            ['name' => 'سارا محمدی', 'slug' => 'sara-mohammadi', 'mobile' => '09120000006', 'leader' => false, 'specialty' => 'صخره‌نوردی'],
            ['name' => 'حسین اکبری', 'slug' => 'hossein-akbari', 'mobile' => '09120000007', 'leader' => true, 'specialty' => 'سرپرستی برنامه'],
            ['name' => 'مریم حسینی', 'slug' => 'maryam-hosseini', 'mobile' => '09120000008', 'leader' => false, 'specialty' => 'یخ‌نوردی'],
        ];

        $coaches = collect($coachPeople)->values()->map(function (array $person, int $index) use ($clubs) {
            $user = User::factory()->create([
                'name' => $person['name'],
                'mobile' => $person['mobile'],
                'username' => $person['slug'],
                'role' => $person['leader'] ? UserRole::Leader : UserRole::Coach,
                'primary_club_id' => $clubs[$index % $clubs->count()]->id,
            ]);

            $club = $clubs[$index % $clubs->count()];
            $club->members()->attach($user->id, ['status' => 'active']);

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

        foreach ($destinations as $index => $destination) {
            DestinationGuide::query()->create([
                'destination_id' => $destination->id,
                'club_id' => $clubs[$index % $clubs->count()]->id,
                'coach_profile_id' => null,
                'guide_type' => 'club',
            ]);

            DestinationGuide::query()->create([
                'destination_id' => $destination->id,
                'club_id' => null,
                'coach_profile_id' => $coaches[$index % $coaches->count()]->id,
                'guide_type' => 'coach',
            ]);
        }

        foreach ($clubs->take(3) as $club) {
            ProgramReport::query()->create([
                'title' => 'گزارش صعود '.$destinations->random()->name.' با '.$club->name,
                'slug' => Str::slug('report-'.$club->slug.'-'.Str::random(5)),
                'excerpt' => 'گزارش کامل برنامه برای دیده‌شدن در گوگل و اطلاع‌رسانی به جامعه کوهنوردی کرمان.',
                'body' => "این گزارش نمونه محتوای سئو‌محور است.\nدر آینده به ثبت‌نام دوره و برنامه باشگاهی متصل می‌شود.",
                'cover_path' => 'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?auto=format&fit=crop&w=1200&q=80',
                'destination_id' => $destinations->random()->id,
                'club_id' => $club->id,
                'author_id' => $admin->id,
                'published_at' => now()->subDays(random_int(2, 40)),
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

        $hezar = $destinations->firstWhere('slug', 'hezar');
        $joopar = $destinations->firstWhere('slug', 'joopar');
        $lalezar = $destinations->firstWhere('slug', 'lalezar');

        Accommodation::query()->create([
            'name' => 'بوم‌گردی دامنه هزار',
            'slug' => 'eco-hezar',
            'type' => LodgingType::EcoLodge,
            'summary' => 'خانه بوم‌گردی نزدیک مسیر صعود هزار برای شب‌مانی قبل از برنامه.',
            'description' => 'اقامتگاه بوم‌گردی با موقعیت جغرافیایی ثبت‌شده؛ مناسب تیم‌های باشگاهی.',
            'city' => 'راین',
            'cover_path' => 'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?auto=format&fit=crop&w=1200&q=80',
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
            'cover_path' => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=1200&q=80',
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
            'cover_path' => 'https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?auto=format&fit=crop&w=1200&q=80',
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
            'cover_path' => 'https://images.unsplash.com/photo-1504280390367-361c6d9f38f4?auto=format&fit=crop&w=1200&q=80',
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
            'cover_path' => 'https://images.unsplash.com/photo-1519681393784-d120267933ba?auto=format&fit=crop&w=1200&q=80',
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
            'cover_path' => 'https://images.unsplash.com/photo-1493246507139-91e8fad9978e?auto=format&fit=crop&w=1200&q=80',
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
            'role' => UserRole::Climber,
            'primary_club_id' => $clubs->first()->id,
        ]);
    }
}
