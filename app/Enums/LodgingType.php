<?php

namespace App\Enums;

enum LodgingType: string
{
    case EcoLodge = 'eco_lodge';
    case Hotel = 'hotel';
    case Guesthouse = 'guesthouse';
    case Camp = 'camp';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::EcoLodge => 'بوم‌گردی',
            self::Hotel => 'هتل',
            self::Guesthouse => 'مهمان‌سرا',
            self::Camp => 'کمپ / اقامت موقت',
            self::Other => 'سایر',
        };
    }
}
