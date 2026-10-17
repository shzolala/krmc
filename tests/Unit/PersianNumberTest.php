<?php

namespace Tests\Unit;

use App\Support\PersianNumber;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PersianNumberTest extends TestCase
{
    #[Test]
    public function it_converts_western_digits_to_persian(): void
    {
        $this->assertSame('۱۲۳', PersianNumber::digits(123));
        $this->assertSame('۴۵۰۱', fa_digits(4501));
        $this->assertSame('۲۰۲۶/۱۰/۱۷', fa_digits('2026/10/17'));
    }

    #[Test]
    public function it_formats_numbers_with_persian_digits(): void
    {
        $this->assertSame('۱,۲۳۴', fa_num(1234));
        $this->assertSame('۴.۵', fa_num(4.5, 1));
        $this->assertSame('', fa_num(null));
    }
}
