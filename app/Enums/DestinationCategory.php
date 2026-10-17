<?php

namespace App\Enums;

enum DestinationCategory: string
{
    case Peak = 'peak';
    case NatureSpot = 'nature_spot';

    public function label(): string
    {
        return match ($this) {
            self::Peak => 'قله',
            self::NatureSpot => 'نقطه طبیعی',
        };
    }
}
