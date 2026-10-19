<?php

namespace Database\Seeders;

use App\Enums\FundIncomeSource;
use App\Enums\SponsorStatus;
use App\Models\FundIncome;
use App\Models\Sponsor;
use App\Models\SponsorshipPackage;
use Illuminate\Database\Seeder;

/**
 * Seeds sponsorship packages and fictional sponsors with sample payments. Safe to re-run.
 */
class SponsorSeeder extends Seeder
{
    public function run(): void
    {
        $packages = [];

        foreach ([
            ['حامی برنزی', 20_000_000, 'شروع همراهی با صندوق کوهستان.', ['نمایش لوگو در صفحه اصلی', 'صفحه معرفی اختصاصی', 'لینک به وب‌سایت حامی']],
            ['حامی نقره‌ای', 60_000_000, 'حمایت پایدار از برنامه‌های آموزشی.', ['همه مزایای حامی برنزی', 'ذکر نام در گزارش‌های برنامه‌ها', 'معرفی در خبرنامه انجمن']],
            ['حامی طلایی', 150_000_000, 'شریک اصلی طرح‌های مربی‌پروری و توسعه گردشگری.', ['همه مزایای حامی نقره‌ای', 'ردیف ویژه در فهرست حامیان', 'نام در کمپین‌های جذب سرمایه', 'دعوت به رویدادهای راهبردی']],
        ] as $order => [$name, $min, $description, $benefits]) {
            $packages[$name] = SponsorshipPackage::query()->updateOrCreate(
                ['name' => $name],
                ['min_amount' => $min, 'description' => $description, 'benefits' => $benefits, 'sort_order' => $order, 'is_active' => true],
            );
        }

        $sponsors = [
            ['kavir', 'گروه صنعتی کویر کرمان', 'حامی طلایی', 160_000_000, true, 'گروهی صنعتی با بیش از دو دهه سابقه در استان که از طرح‌های زیست‌محیطی و گردشگری حمایت می‌کند.', 'تأمین هزینه کارگاه‌های مربی‌پروری و تجهیزات ایمنی برنامه‌های مسیر هزار.'],
            ['hezar', 'تعاونی تجهیزات هزار', 'حامی نقره‌ای', 70_000_000, true, 'تعاونی تولید و توزیع تجهیزات کوهنوردی با شعبه‌هایی در کرمان.', 'اهدای تجهیزات به دوره‌های آموزشی و حمایت از برنامه‌های باشگاه‌های جوان.'],
            ['ghollah', 'شرکت فناوری قله', 'حامی نقره‌ای', 65_000_000, false, 'شرکت نرم‌افزاری فعال در حوزه‌ی خدمات دیجیتال.', 'پشتیبانی فنی از سامانه‌ی انجمن و بخشی از هزینه‌های توسعه سایت.'],
            ['joopar', 'مجتمع گردشگری جوپار', 'حامی برنزی', 25_000_000, false, 'مجموعه‌ی اقامتی و پذیرایی در مسیر ورودی جوپار.', 'تخفیف ویژه‌ی اقامت برای شرکت‌کنندگان برنامه‌های انجمن.'],
            ['tabiat', 'انجمن حامیان طبیعت', 'حامی برنزی', 20_000_000, false, 'سازمان مردم‌نهاد فعال در حفاظت از محیط‌زیست کوهستان.', 'همکاری در برنامه‌های پاکسازی مسیرهای کوهستانی.'],
            ['lalehzar', 'شرکت معدنی لالهزار', 'حامی نقره‌ای', 80_000_000, false, 'شرکت معدنی با تمرکز بر مسئولیت اجتماعی در منطقه.', 'حمایت از ایمنی و نجات کوهستان و تأمین بخشی از تجهیزات امدادی.'],
            ['rahkooh', 'مؤسسه آموزشی راه کوه', 'حامی برنزی', 22_000_000, false, 'مؤسسه‌ی آموزش مهارت‌های فنی و کوهنوردی.', 'اختصاص فضای آموزشی برای دوره‌های مربیگری.'],
            ['sepehr', 'تولیدی پوشاک سپهر', 'حامی برنزی', 30_000_000, false, 'تولیدکننده‌ی پوشاک ورزشی و کوهستان.', 'تأمین پوشاک مربیان و سرپرستان برنامه‌های انجمن.'],
        ];

        foreach ($sponsors as $order => [$logo, $name, $packageName, $amount, $featured, $description, $support]) {
            $sponsor = Sponsor::query()->updateOrCreate(
                ['slug' => 'sponsor-'.$logo],
                [
                    'name' => $name,
                    'logo_path' => 'images/sponsors/'.$logo.'.svg',
                    'website' => 'https://example.com/'.$logo,
                    'summary' => $description,
                    'description' => $description.' این معرفی نمونه برای نمایش در صفحه‌ی اختصاصی حامی است.',
                    'support_note' => $support,
                    'sponsorship_package_id' => $packages[$packageName]->id,
                    'pledge_amount' => $amount,
                    'show_amount' => $logo !== 'ghollah',
                    'status' => SponsorStatus::Active,
                    'is_featured' => $featured,
                    'sort_order' => $order,
                    'started_at' => now()->subMonths(2 + $order)->toDateString(),
                ],
            );

            $sponsor->incomes()->delete();

            FundIncome::query()->create([
                'source' => FundIncomeSource::Sponsorship,
                'amount' => $amount,
                'received_at' => now()->subMonths(2 + $order)->toDateString(),
                'sponsor_id' => $sponsor->id,
                'note' => 'حمایت '.$name,
            ]);
        }
    }
}
