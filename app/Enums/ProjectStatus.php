<?php

namespace App\Enums;

enum ProjectStatus: string
{
    case Planned = 'planned';
    case Active = 'active';
    case Completed = 'completed';

    public function label(): string
    {
        return match ($this) {
            self::Planned => 'در دست طراحی',
            self::Active => 'در حال جذب حمایت',
            self::Completed => 'تکمیل‌شده',
        };
    }
}
