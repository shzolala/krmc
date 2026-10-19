<?php

namespace App\Support;

use Carbon\CarbonInterface;

class JalaliDate
{
    private const MONTHS = [
        1 => 'فروردین', 2 => 'اردیبهشت', 3 => 'خرداد', 4 => 'تیر', 5 => 'مرداد', 6 => 'شهریور',
        7 => 'مهر', 8 => 'آبان', 9 => 'آذر', 10 => 'دی', 11 => 'بهمن', 12 => 'اسفند',
    ];

    /**
     * @return array{0: int, 1: int, 2: int}
     */
    public static function fromGregorian(int $year, int $month, int $day): array
    {
        $cumulative = [0, 31, 59, 90, 120, 151, 181, 212, 243, 273, 304, 334];
        $gy2 = $month > 2 ? $year + 1 : $year;
        $days = 355666 + (365 * $year) + intdiv($gy2 + 3, 4) - intdiv($gy2 + 99, 100)
            + intdiv($gy2 + 399, 400) + $day + $cumulative[$month - 1];

        $jy = -1595 + (33 * intdiv($days, 12053));
        $days %= 12053;
        $jy += 4 * intdiv($days, 1461);
        $days %= 1461;

        if ($days > 365) {
            $jy += intdiv($days - 1, 365);
            $days = ($days - 1) % 365;
        }

        if ($days < 186) {
            $jm = 1 + intdiv($days, 31);
            $jd = 1 + ($days % 31);
        } else {
            $jm = 7 + intdiv($days - 186, 30);
            $jd = 1 + (($days - 186) % 30);
        }

        return [$jy, $jm, $jd];
    }

    public static function currentYear(): int
    {
        $now = now();

        return self::fromGregorian($now->year, $now->month, $now->day)[0];
    }

    public static function format(?CarbonInterface $date, bool $withTime = false, bool $monthName = false): string
    {
        if ($date === null) {
            return '—';
        }

        [$year, $month, $day] = self::fromGregorian($date->year, $date->month, $date->day);

        $text = $monthName
            ? $day.' '.self::MONTHS[$month].' '.$year
            : sprintf('%04d/%02d/%02d', $year, $month, $day);

        if ($withTime) {
            $text .= ' — '.$date->format('H:i');
        }

        return PersianNumber::digits($text);
    }

    /**
     * Compact range such as "۱۴ تا ۱۶ مهر ۱۴۰۵" that drops repeated month and year.
     */
    public static function range(?CarbonInterface $start, ?CarbonInterface $end): string
    {
        if ($start === null) {
            return '—';
        }

        if ($end === null || $start->isSameDay($end)) {
            return self::format($start, monthName: true);
        }

        [$sy, $sm, $sd] = self::fromGregorian($start->year, $start->month, $start->day);
        [$ey, $em, $ed] = self::fromGregorian($end->year, $end->month, $end->day);

        $text = match (true) {
            $sy === $ey && $sm === $em => "{$sd} تا {$ed} ".self::MONTHS[$em]." {$ey}",
            $sy === $ey => "{$sd} ".self::MONTHS[$sm]." تا {$ed} ".self::MONTHS[$em]." {$ey}",
            default => self::format($start, monthName: true).' تا '.self::format($end, monthName: true),
        };

        return PersianNumber::digits($text);
    }

    /**
     * Inclusive number of calendar days between two dates.
     */
    public static function days(CarbonInterface $start, CarbonInterface $end): int
    {
        return (int) $start->copy()->startOfDay()->diffInDays($end->copy()->startOfDay()) + 1;
    }
}
