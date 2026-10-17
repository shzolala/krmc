<?php

namespace App\Enums;

enum MountainRegionType: string
{
    case Massif = 'massif';
    case MountainRange = 'mountain_range';
    case VolcanicMassif = 'volcanic_massif';
    case NationalPark = 'national_park';
    case VolcanicMountainRange = 'volcanic_mountain_range';
    case MountainRangeAndPlaya = 'mountain_range_and_playa';
    case CanyonAndMountains = 'canyon_and_mountains';
    case Desert = 'desert';
    case WetlandPlaya = 'wetland_playa';
    case UrbanHills = 'urban_hills';

    public function label(): string
    {
        return match ($this) {
            self::Massif => 'توده کوهستانی',
            self::MountainRange => 'رشته‌کوه',
            self::VolcanicMassif => 'توده آتشفشانی',
            self::NationalPark => 'پارک ملی',
            self::VolcanicMountainRange => 'رشته‌کوه آتشفشانی',
            self::MountainRangeAndPlaya => 'رشته‌کوه و پلایا',
            self::CanyonAndMountains => 'دره و کوهستان',
            self::Desert => 'بیابان',
            self::WetlandPlaya => 'تالاب و پلایا',
            self::UrbanHills => 'تپه‌های شهری',
        };
    }
}
