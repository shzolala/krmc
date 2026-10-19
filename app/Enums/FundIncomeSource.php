<?php

namespace App\Enums;

enum FundIncomeSource: string
{
    case Membership = 'membership';
    case Sponsorship = 'sponsorship';
    case Donation = 'donation';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Membership => 'حق عضویت',
            self::Sponsorship => 'اسپانسرشیپ',
            self::Donation => 'کمک مالی',
            self::Other => 'سایر درآمدها',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Membership => 'bg-blue-500',
            self::Sponsorship => 'bg-violet-500',
            self::Donation => 'bg-emerald-500',
            self::Other => 'bg-zinc-400',
        };
    }
}
