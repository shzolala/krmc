<?php

namespace Tests\Unit;

use App\Support\JalaliDate;
use Carbon\Carbon;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class JalaliDateRangeTest extends TestCase
{
    #[Test]
    public function it_collapses_repeated_month_and_year(): void
    {
        $this->assertSame('۱۴ تا ۱۶ مهر ۱۴۰۵', JalaliDate::range(Carbon::parse('2026-10-06'), Carbon::parse('2026-10-08')));
        $this->assertSame('۲۵ مهر تا ۲ آبان ۱۴۰۵', JalaliDate::range(Carbon::parse('2026-10-17'), Carbon::parse('2026-10-24')));
        $this->assertSame('۱۴ مهر ۱۴۰۵', JalaliDate::range(Carbon::parse('2026-10-06 08:00'), Carbon::parse('2026-10-06 18:00')));
        $this->assertSame('—', JalaliDate::range(null, null));
    }

    #[Test]
    public function it_counts_inclusive_days(): void
    {
        $this->assertSame(3, JalaliDate::days(Carbon::parse('2026-10-06 20:00'), Carbon::parse('2026-10-08 05:00')));
    }
}
