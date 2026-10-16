<?php

namespace App\Support;

use Illuminate\Support\Str;

class MobileNumber
{
    /**
     * Normalize Iranian mobile numbers to 09XXXXXXXXX.
     */
    public static function normalize(?string $value): ?string
    {
        if ($value === null || trim($value) === '') {
            return null;
        }

        $digits = self::toEnglishDigits($value);
        $digits = preg_replace('/\D+/', '', $digits) ?? '';

        if (Str::startsWith($digits, '0098')) {
            $digits = '0'.Str::substr($digits, 4);
        }

        if (Str::startsWith($digits, '98') && Str::length($digits) === 12) {
            $digits = '0'.Str::substr($digits, 2);
        }

        if (Str::startsWith($digits, '9') && Str::length($digits) === 10) {
            $digits = '0'.$digits;
        }

        return $digits;
    }

    public static function isValid(?string $value): bool
    {
        $normalized = self::normalize($value);

        return is_string($normalized) && (bool) preg_match('/^09\d{9}$/', $normalized);
    }

    public static function rule(): string
    {
        return 'regex:/^09\d{9}$/';
    }

    private static function toEnglishDigits(string $value): string
    {
        return strtr($value, [
            '۰' => '0', '۱' => '1', '۲' => '2', '۳' => '3', '۴' => '4',
            '۵' => '5', '۶' => '6', '۷' => '7', '۸' => '8', '۹' => '9',
            '٠' => '0', '١' => '1', '٢' => '2', '٣' => '3', '٤' => '4',
            '٥' => '5', '٦' => '6', '٧' => '7', '٨' => '8', '٩' => '9',
        ]);
    }
}
