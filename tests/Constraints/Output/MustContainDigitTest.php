<?php

declare(strict_types=1);

namespace Tests\Constraints\Output;

use Myerscode\Utilities\Random\Constraints\Output\MustContainDigit;
use Tests\BaseTestSuite;

final class MustContainDigitTest extends BaseTestSuite
{
    public function testPassesWithDefaultMinimum(): void
    {
        $mustContainDigit = new MustContainDigit();
        $this->assertTrue($mustContainDigit->passes('abc1'));
    }

    public function testFailsWithNoDigits(): void
    {
        $mustContainDigit = new MustContainDigit();
        $this->assertFalse($mustContainDigit->passes('abcdef'));
    }

    public function testPassesWithCustomMinimum(): void
    {
        $mustContainDigit = new MustContainDigit(3);
        $this->assertTrue($mustContainDigit->passes('a1b2c3'));
    }

    public function testFailsWhenBelowCustomMinimum(): void
    {
        $mustContainDigit = new MustContainDigit(3);
        $this->assertFalse($mustContainDigit->passes('a1b2cd'));
    }

    public function testPassesWithAllDigits(): void
    {
        $mustContainDigit = new MustContainDigit(5);
        $this->assertTrue($mustContainDigit->passes('12345'));
    }

    public function testFailsWithEmptyString(): void
    {
        $mustContainDigit = new MustContainDigit();
        $this->assertFalse($mustContainDigit->passes(''));
    }
}
