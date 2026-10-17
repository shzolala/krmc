<?php

namespace App\Support;

class PersianNumber
{
    private const WESTERN = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];

    private const PERSIAN = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];

    public static function digits(mixed $value): string
    {
        if ($value === null) {
            return '';
        }

        return str_replace(self::WESTERN, self::PERSIAN, (string) $value);
    }

    public static function format(int|float|string|null $value, int $decimals = 0): string
    {
        if ($value === null || $value === '') {
            return '';
        }

        if (! is_numeric($value)) {
            return self::digits($value);
        }

        return self::digits(number_format((float) $value, $decimals, '.', ','));
    }
}
