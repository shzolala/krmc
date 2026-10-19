<?php

namespace App\Enums;

enum SponsorStatus: string
{
    case Pending = 'pending';
    case Active = 'active';
    case Ended = 'ended';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'در انتظار تأیید',
            self::Active => 'فعال',
            self::Ended => 'پایان‌یافته',
        };
    }
}
