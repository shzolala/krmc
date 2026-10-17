<?php

namespace App\Enums;

enum ProgramStatus: string
{
    case Upcoming = 'upcoming';
    case Ongoing = 'ongoing';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Upcoming => 'پیش‌رو',
            self::Ongoing => 'در حال برگزاری',
            self::Completed => 'برگزارشده',
            self::Cancelled => 'لغوشده',
        };
    }
}
