<?php

declare(strict_types=1);

namespace Tests\Constraints\Output;

use Myerscode\Utilities\Random\Constraints\Output\MustContainUppercase;
use Tests\BaseTestSuite;

final class MustContainUppercaseTest extends BaseTestSuite
{
    public function testDoesNotCountLowercase(): void
    {
        $mustContainUppercase = new MustContainUppercase(2);
        $this->assertFalse($mustContainUppercase->passes('Aabcde'));
    }

    public function testFailsWhenBelowCustomMinimum(): void
    {
        $mustContainUppercase = new MustContainUppercase(3);
        $this->assertFalse($mustContainUppercase->passes('ABcdef'));
    }

    public function testFailsWithEmptyString(): void
    {
        $mustContainUppercase = new MustContainUppercase();
        $this->assertFalse($mustContainUppercase->passes(''));
    }

    public function testFailsWithNoUppercase(): void
    {
        $mustContainUppercase = new MustContainUppercase();
        $this->assertFalse($mustContainUppercase->passes('abcdef'));
    }

    public function testPassesWithCustomMinimum(): void
    {
        $mustContainUppercase = new MustContainUppercase(3);
        $this->assertTrue($mustContainUppercase->passes('ABCdef'));
    }
    public function testPassesWithDefaultMinimum(): void
    {
        $mustContainUppercase = new MustContainUppercase();
        $this->assertTrue($mustContainUppercase->passes('abcD'));
    }
}
