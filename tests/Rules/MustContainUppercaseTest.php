<?php

declare(strict_types=1);

namespace Tests\Rules;

use Myerscode\Utilities\Random\Rules\MustContainUppercase;
use Tests\BaseTestSuite;

class MustContainUppercaseTest extends BaseTestSuite
{
    public function testPassesWithDefaultMinimum(): void
    {
        $rule = new MustContainUppercase();
        $this->assertTrue($rule->passes('abcD'));
    }

    public function testFailsWithNoUppercase(): void
    {
        $rule = new MustContainUppercase();
        $this->assertFalse($rule->passes('abcdef'));
    }

    public function testPassesWithCustomMinimum(): void
    {
        $rule = new MustContainUppercase(3);
        $this->assertTrue($rule->passes('ABCdef'));
    }

    public function testFailsWhenBelowCustomMinimum(): void
    {
        $rule = new MustContainUppercase(3);
        $this->assertFalse($rule->passes('ABcdef'));
    }

    public function testDoesNotCountLowercase(): void
    {
        $rule = new MustContainUppercase(2);
        $this->assertFalse($rule->passes('Aabcde'));
    }

    public function testFailsWithEmptyString(): void
    {
        $rule = new MustContainUppercase();
        $this->assertFalse($rule->passes(''));
    }
}
