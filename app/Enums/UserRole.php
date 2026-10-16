<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case Climber = 'climber';
    case Club = 'club';
    case Coach = 'coach';
    case Leader = 'leader';
    case Shop = 'shop';
    case Organization = 'organization';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'مدیر',
            self::Climber => 'کوهنورد',
            self::Club => 'باشگاه',
            self::Coach => 'مربی',
            self::Leader => 'سرپرست',
            self::Shop => 'فروشگاه',
            self::Organization => 'ارگان',
        };
    }

    public function hasPublicDirectoryProfile(): bool
    {
        return in_array($this, [
            self::Club,
            self::Coach,
            self::Leader,
            self::Shop,
            self::Organization,
        ], true);
    }
}
