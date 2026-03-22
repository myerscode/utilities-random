<?php

declare(strict_types=1);

namespace Tests\Rules;

use Myerscode\Utilities\Random\Rules\MustContainLetter;
use Tests\BaseTestSuite;

class MustContainLetterTest extends BaseTestSuite
{
    public function testPassesWithDefaultMinimum(): void
    {
        $rule = new MustContainLetter();
        $this->assertTrue($rule->passes('1a23'));
    }

    public function testFailsWithNoLetters(): void
    {
        $rule = new MustContainLetter();
        $this->assertFalse($rule->passes('12345'));
    }

    public function testPassesWithCustomMinimum(): void
    {
        $rule = new MustContainLetter(3);
        $this->assertTrue($rule->passes('a1b2c'));
    }

    public function testFailsWhenBelowCustomMinimum(): void
    {
        $rule = new MustContainLetter(3);
        $this->assertFalse($rule->passes('a1b23'));
    }

    public function testCountsBothCases(): void
    {
        $rule = new MustContainLetter(4);
        $this->assertTrue($rule->passes('AaBb'));
    }

    public function testFailsWithEmptyString(): void
    {
        $rule = new MustContainLetter();
        $this->assertFalse($rule->passes(''));
    }
}
