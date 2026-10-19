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

if (! function_exists('fa_date_range')) {
    /**
     * Format a Jalali date range compactly, e.g. "۱۴ تا ۱۶ مهر ۱۴۰۵".
     */
    function fa_date_range(?CarbonInterface $start, ?CarbonInterface $end): string
    {
        return JalaliDate::range($start, $end);
    }
}

if (! function_exists('fa_money')) {
    /**
     * Format an amount in Toman with Persian digits.
     */
    function fa_money(int|float|null $value): string
    {
        return PersianNumber::format($value ?? 0).' تومان';
    }
}
