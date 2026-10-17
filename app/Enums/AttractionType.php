<?php

namespace App\Enums;

enum AttractionType: string
{
    case Historical = 'historical';
    case Religious = 'religious';
    case Museum = 'museum';
    case Art = 'art';
    case Memorial = 'memorial';

    public function label(): string
    {
        return match ($this) {
            self::Historical => 'تاریخی',
            self::Religious => 'مذهبی',
            self::Museum => 'موزه',
            self::Art => 'هنری',
            self::Memorial => 'یادمان',
        };
    }
}
