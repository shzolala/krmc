<?php

namespace App\Enums;

enum ProjectCategory: string
{
    case CoachTraining = 'coach_training';
    case ShelterPreservation = 'shelter_preservation';
    case Environment = 'environment';
    case TourismPromotion = 'tourism_promotion';
    case SafetyRescue = 'safety_rescue';

    public function label(): string
    {
        return match ($this) {
            self::CoachTraining => 'تربیت مربی',
            self::ShelterPreservation => 'حفظ و نگهداری پناهگاه‌ها',
            self::Environment => 'محیط زیست',
            self::TourismPromotion => 'معرفی مکان‌های دیدنی',
            self::SafetyRescue => 'ایمنی و نجات',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::CoachTraining => 'برگزاری دوره‌ها و ارتقای مربیان و سرپرستان کوهنوردی',
            self::ShelterPreservation => 'مرمت، تجهیز و نگهداری پناهگاه‌های مسیرهای کوهستانی',
            self::Environment => 'پاکسازی مسیرها و حفاظت از طبیعت کوهستان',
            self::TourismPromotion => 'مستندسازی و معرفی جاذبه‌ها و مقاصد کوهستانی کرمان',
            self::SafetyRescue => 'تجهیزات و آموزش امداد و نجات در کوهستان',
        };
    }

    /**
     * Icon name from the public site icon set.
     */
    public function icon(): string
    {
        return match ($this) {
            self::CoachTraining => 'academic',
            self::ShelterPreservation => 'shelter',
            self::Environment => 'sun',
            self::TourismPromotion => 'map',
            self::SafetyRescue => 'flag',
        };
    }
}
