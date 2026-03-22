<?php

declare(strict_types=1);

namespace Tests\Rules;

use Myerscode\Utilities\Random\Rules\MustContainDigit;
use Tests\BaseTestSuite;

class MustContainDigitTest extends BaseTestSuite
{
    public function testPassesWithDefaultMinimum(): void
    {
        $rule = new MustContainDigit();
        $this->assertTrue($rule->passes('abc1'));
    }

    public function testFailsWithNoDigits(): void
    {
        $rule = new MustContainDigit();
        $this->assertFalse($rule->passes('abcdef'));
    }

    public function testPassesWithCustomMinimum(): void
    {
        $rule = new MustContainDigit(3);
        $this->assertTrue($rule->passes('a1b2c3'));
    }

    public function testFailsWhenBelowCustomMinimum(): void
    {
        $rule = new MustContainDigit(3);
        $this->assertFalse($rule->passes('a1b2cd'));
    }

    public function testPassesWithAllDigits(): void
    {
        $rule = new MustContainDigit(5);
        $this->assertTrue($rule->passes('12345'));
    }

    public function testFailsWithEmptyString(): void
    {
        $rule = new MustContainDigit();
        $this->assertFalse($rule->passes(''));
    }
}
