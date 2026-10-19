<?php

namespace App\Support\Admin;

use App\Support\Admin\Resources\AccommodationResource;
use App\Support\Admin\Resources\AttractionResource;
use App\Support\Admin\Resources\ClimbBadgeResource;
use App\Support\Admin\Resources\ClubResource;
use App\Support\Admin\Resources\CoachResource;
use App\Support\Admin\Resources\DestinationResource;
use App\Support\Admin\Resources\FundCampaignResource;
use App\Support\Admin\Resources\FundExpenseResource;
use App\Support\Admin\Resources\FundIncomeResource;
use App\Support\Admin\Resources\HeroSlideResource;
use App\Support\Admin\Resources\LedgerAccessResource;
use App\Support\Admin\Resources\MembershipDueResource;
use App\Support\Admin\Resources\MembershipPlanResource;
use App\Support\Admin\Resources\MountainRegionResource;
use App\Support\Admin\Resources\PeakAscentResource;
use App\Support\Admin\Resources\ProgramReportResource;
use App\Support\Admin\Resources\ProgramResource;
use App\Support\Admin\Resources\ProjectActivityResource;
use App\Support\Admin\Resources\ShelterResource;
use App\Support\Admin\Resources\ShopResource;
use App\Support\Admin\Resources\SponsorResource;
use App\Support\Admin\Resources\SponsorshipPackageResource;
use App\Support\Admin\Resources\SupportProjectResource;

class AdminResources
{
    /**
     * @var array<string, string>
     */
    public const GROUPS = [
        'site' => 'ظاهر سایت',
        'directory' => 'دایرکتوری',
        'geography' => 'جغرافیا و گردشگری',
        'activity' => 'برنامه‌ها و گزارش‌ها',
        'peaks' => 'نردبان قله‌ها',
        'projects' => 'طرح‌های حمایتی',
        'finance' => 'صندوق و امور مالی',
    ];

    /**
     * @return array<string, AdminResource>
     */
    public static function all(): array
    {
        return collect([
            new HeroSlideResource,
            new ClubResource,
            new CoachResource,
            new ShopResource,
            new MountainRegionResource,
            new DestinationResource,
            new AttractionResource,
            new AccommodationResource,
            new ShelterResource,
            new ProgramResource,
            new ProgramReportResource,
            new ClimbBadgeResource,
            new PeakAscentResource,
            new SupportProjectResource,
            new ProjectActivityResource,
            new MembershipPlanResource,
            new MembershipDueResource,
            new FundIncomeResource,
            new FundExpenseResource,
            new FundCampaignResource,
            new SponsorResource,
            new SponsorshipPackageResource,
            new LedgerAccessResource,
        ])->keyBy(fn (AdminResource $resource): string => $resource->key())->all();
    }

    public static function find(string $key): ?AdminResource
    {
        return self::all()[$key] ?? null;
    }

    /**
     * @return array<string, list<AdminResource>>
     */
    public static function grouped(): array
    {
        $grouped = [];

        foreach (self::all() as $resource) {
            $grouped[$resource->group()][] = $resource;
        }

        return $grouped;
    }

    /**
     * @return list<string>
     */
    public static function keys(): array
    {
        return array_keys(self::all());
    }
}
