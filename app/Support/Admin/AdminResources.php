<?php

namespace App\Support\Admin;

use App\Support\Admin\Resources\AccommodationResource;
use App\Support\Admin\Resources\AttractionResource;
use App\Support\Admin\Resources\ClubResource;
use App\Support\Admin\Resources\CoachResource;
use App\Support\Admin\Resources\DestinationResource;
use App\Support\Admin\Resources\MountainRegionResource;
use App\Support\Admin\Resources\ProgramReportResource;
use App\Support\Admin\Resources\ProgramResource;
use App\Support\Admin\Resources\ShelterResource;
use App\Support\Admin\Resources\ShopResource;

class AdminResources
{
    /**
     * @var array<string, string>
     */
    public const GROUPS = [
        'directory' => 'دایرکتوری',
        'geography' => 'جغرافیا و گردشگری',
        'activity' => 'برنامه‌ها و گزارش‌ها',
    ];

    /**
     * @return array<string, AdminResource>
     */
    public static function all(): array
    {
        return collect([
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
