<?php

declare(strict_types=1);

namespace Tests\Constraints\Output;

use Myerscode\Utilities\Random\Constraints\Output\MustContainLetter;
use Tests\BaseTestSuite;

final class MustContainLetterTest extends BaseTestSuite
{
    public function testCountsBothCases(): void
    {
        $mustContainLetter = new MustContainLetter(4);
        $this->assertTrue($mustContainLetter->passes('AaBb'));
    }

    public function testFailsWhenBelowCustomMinimum(): void
    {
        $mustContainLetter = new MustContainLetter(3);
        $this->assertFalse($mustContainLetter->passes('a1b23'));
    }

    public function testFailsWithEmptyString(): void
    {
        $mustContainLetter = new MustContainLetter();
        $this->assertFalse($mustContainLetter->passes(''));
    }

    public function testFailsWithNoLetters(): void
    {
        $mustContainLetter = new MustContainLetter();
        $this->assertFalse($mustContainLetter->passes('12345'));
    }

    public function testPassesWithCustomMinimum(): void
    {
        $mustContainLetter = new MustContainLetter(3);
        $this->assertTrue($mustContainLetter->passes('a1b2c'));
    }

    public function testPassesWithDefaultMinimum(): void
    {
        $mustContainLetter = new MustContainLetter();
        $this->assertTrue($mustContainLetter->passes('1a23'));
    }
}
