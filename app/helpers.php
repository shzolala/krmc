<?php

use App\Support\JalaliDate;
use App\Support\PersianNumber;
use Carbon\CarbonInterface;

if (! function_exists('fa_digits')) {
    /**
     * Convert Western digits in a value to Persian digits.
     */
    function fa_digits(mixed $value): string
    {
        return PersianNumber::digits($value);
    }
}

if (! function_exists('fa_num')) {
    /**
     * Format a number and convert its digits to Persian.
     */
    function fa_num(int|float|string|null $value, int $decimals = 0): string
    {
        return PersianNumber::format($value, $decimals);
    }
}

if (! function_exists('fa_date')) {
    /**
     * Format a date in the Jalali calendar with Persian digits.
     */
    function fa_date(?CarbonInterface $date, bool $withTime = false, bool $monthName = false): string
    {
        return JalaliDate::format($date, $withTime, $monthName);
    }
}
