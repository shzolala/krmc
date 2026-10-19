<?php

namespace Database\Seeders;

use App\Enums\FundExpenseCategory;
use App\Enums\FundIncomeSource;
use App\Enums\ProjectCategory;
use App\Enums\ProjectStatus;
use App\Models\FundExpense;
use App\Models\FundIncome;
use App\Models\ProjectActivity;
use App\Models\Sponsor;
use App\Models\SupportProject;
use Illuminate\Database\Seeder;

/**
 * Seeds support projects with activities, budgets and sponsor contributions (cash and bartered
 * in-kind, per round). Needs the sponsors from SponsorSeeder. Safe to re-run.
 */
class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(SponsorSeeder::class);

        $projects = [
            'coach-training' => [ProjectCategory::CoachTraining, 'مربی‌پروری کرمان', 'تربیت ۴۰ مربی جوان کوهنوردی در سه سطح فنی', 120_000_000, ProjectStatus::Active, true, [
                ['برگزاری کارگاه مربیگری درجه ۳', 'نخستین دوره با شرکت ۲۴ نفر از باشگاه‌های استان.', 70],
                ['دوره‌ی کمک‌های اولیه در کوهستان', 'آموزش عملی با همکاری مربیان نجات.', 35],
                ['آزمون و صدور گواهی', 'آزمون مهارت فنی و ارزیابی شرکت‌کنندگان.', 10],
            ]],
            'shelter' => [ProjectCategory::ShelterPreservation, 'مرمت پناهگاه‌های مسیر هزار', 'ترمیم، عایق‌کاری و تجهیز دو پناهگاه مسیر صعود هزار', 90_000_000, ProjectStatus::Active, true, [
                ['بازدید فنی و برآورد مرمت', 'ارزیابی وضعیت دو پناهگاه توسط گروه فنی.', 80],
                ['عایق‌کاری بام پناهگاه نخست', 'اجرای عایق با مشارکت داوطلبان باشگاه‌ها.', 25],
            ]],
            'environment' => [ProjectCategory::Environment, 'پاکسازی مسیرهای جوپار', 'پاکسازی دوره‌ای مسیرها و نصب سطل‌های جمع‌آوری زباله', 40_000_000, ProjectStatus::Active, false, [
                ['پاکسازی بهاره‌ی مسیر', 'بیش از ۸۰ داوطلب و جمع‌آوری ۳۰۰ کیلو زباله.', 45],
            ]],
            'tourism' => [ProjectCategory::TourismPromotion, 'مستندسازی جاذبه‌های دیدنی کرمان', 'تهیه‌ی عکس و فیلم، نقشه و معرفی‌نامه‌ی مکان‌های دیدنی', 60_000_000, ProjectStatus::Planned, false, [
                ['تدوین فهرست مکان‌ها', 'فهرست‌برداری اولیه از ۳۰ جاذبه‌ی کوهستانی.', 20],
            ]],
            'rescue' => [ProjectCategory::SafetyRescue, 'تجهیز تیم امداد کوهستان', 'خرید تجهیزات ایمنی و برگزاری مانور نجات', 80_000_000, ProjectStatus::Completed, false, [
                ['خرید تجهیزات نجات', 'طناب، هارنس و کیف کمک‌های اولیه.', 100],
                ['مانور امداد و نجات', 'مانور مشترک با تیم‌های نجات استان.', 60],
            ]],
        ];

        $saved = [];

        foreach ($projects as $key => [$category, $title, $summary, $budget, $status, $featured, $activities]) {
            $project = SupportProject::query()->updateOrCreate(
                ['slug' => 'project-'.$key],
                [
                    'title' => $title,
                    'category' => $category,
                    'summary' => $summary,
                    'description' => $summary.'. این طرح با مشارکت باشگاه‌ها، مربیان و حمایت ارگان‌ها و شرکت‌ها اجرا می‌شود و گزارش مالی آن برای اعضا شفاف است.',
                    'budget' => $budget,
                    'status' => $status,
                    'is_featured' => $featured,
                    'sort_order' => count($saved),
                    'starts_on' => now()->subMonths(4)->toDateString(),
                ],
            );

            $project->activities()->delete();

            foreach ($activities as [$activityTitle, $description, $daysAgo]) {
                ProjectActivity::query()->create([
                    'support_project_id' => $project->id,
                    'title' => $activityTitle,
                    'description' => $description,
                    'happened_on' => now()->subDays($daysAgo)->toDateString(),
                ]);
            }

            $saved[$key] = $project;
        }

        $sponsors = Sponsor::query()->get()->keyBy('slug');

        // [project, sponsor, round, cash or in-kind description, amount (equivalent), days ago]
        $contributions = [
            ['coach-training', 'kavir', 1, null, 40_000_000, 90],
            ['coach-training', 'kavir', 2, null, 30_000_000, 30],
            ['coach-training', 'sepehr', 1, 'پوشاک مربیان و سرپرستان (۴۰ دست)', 18_000_000, 60],
            ['shelter', 'hezar', 1, null, 30_000_000, 75],
            ['shelter', 'hezar', 2, 'طناب و تجهیزات اتصال (۳۰۰ متر)', 12_000_000, 25],
            ['shelter', 'lalehzar', 1, null, 25_000_000, 50],
            ['environment', 'tabiat', 1, 'نیروی داوطلب و سطل‌های زباله', 8_000_000, 45],
            ['environment', 'joopar', 1, null, 10_000_000, 40],
            ['tourism', 'ghollah', 1, 'طراحی و پشتیبانی فنی صفحه‌ی معرفی مکان‌ها', 15_000_000, 20],
            ['rescue', 'rahkooh', 1, null, 22_000_000, 100],
            ['rescue', 'lalehzar', 2, 'کیف کمک‌های اولیه و تجهیزات نجات', 20_000_000, 85],
        ];

        FundIncome::query()->whereNotNull('support_project_id')->delete();
        FundExpense::query()->whereNotNull('support_project_id')->delete();

        foreach ($contributions as [$projectKey, $sponsorKey, $round, $inKind, $amount, $daysAgo]) {
            $sponsor = $sponsors['sponsor-'.$sponsorKey];

            FundIncome::query()->create([
                'source' => FundIncomeSource::Sponsorship,
                'amount' => $amount,
                'is_in_kind' => $inKind !== null,
                'in_kind_description' => $inKind,
                'installment_no' => $round,
                'received_at' => now()->subDays($daysAgo)->toDateString(),
                'sponsor_id' => $sponsor->id,
                'support_project_id' => $saved[$projectKey]->id,
                'note' => $inKind !== null ? 'تهاتر کالا/خدمات؛ معادل ریالی ثبت شد.' : null,
            ]);
        }

        foreach ([
            ['coach-training', 'کارگاه مربیگری درجه ۳', FundExpenseCategory::CoachTraining, 42_000_000, 65],
            ['shelter', 'مصالح و دستمزد عایق‌کاری', FundExpenseCategory::Equipment, 28_000_000, 20],
            ['rescue', 'خرید تجهیزات نجات', FundExpenseCategory::SafetyRescue, 40_000_000, 90],
        ] as [$projectKey, $title, $category, $amount, $daysAgo]) {
            FundExpense::query()->create([
                'title' => $title,
                'category' => $category,
                'amount' => $amount,
                'spent_at' => now()->subDays($daysAgo)->toDateString(),
                'support_project_id' => $saved[$projectKey]->id,
            ]);
        }
    }
}
