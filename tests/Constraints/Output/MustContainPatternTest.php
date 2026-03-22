<?php

declare(strict_types=1);

namespace Tests\Constraints\Output;

use Myerscode\Utilities\Random\Constraints\Output\MustContainDigit;
use Myerscode\Utilities\Random\Constraints\Output\MustContainLetter;
use Myerscode\Utilities\Random\Constraints\Output\MustContainUppercase;
use Tests\BaseTestSuite;

class MustContainPatternTest extends BaseTestSuite
{
    public function testCanBeSatisfiedByReturnsTrueWhenPoolContainsMatch(): void
    {
        $rule = new MustContainDigit();
        $this->assertTrue($rule->canBeSatisfiedBy('abc123', 5));
    }

    public function testCanBeSatisfiedByReturnsFalseWhenPoolLacksMatch(): void
    {
        $rule = new MustContainDigit();
        $this->assertFalse($rule->canBeSatisfiedBy('abcdef', 5));
    }

    public function testMustContainLetterCanBeSatisfied(): void
    {
        $rule = new MustContainLetter();
        $this->assertTrue($rule->canBeSatisfiedBy('123a', 5));
        $this->assertFalse($rule->canBeSatisfiedBy('12345', 5));
    }

    public function testMustContainUppercaseCanBeSatisfied(): void
    {
        $rule = new MustContainUppercase();
        $this->assertTrue($rule->canBeSatisfiedBy('abcA', 5));
        $this->assertFalse($rule->canBeSatisfiedBy('abcdef', 5));
    }

    public function testCanBeSatisfiedByWithEmptyPool(): void
    {
        $digit = new MustContainDigit();
        $letter = new MustContainLetter();
        $upper = new MustContainUppercase();

        $this->assertFalse($digit->canBeSatisfiedBy('', 5));
        $this->assertFalse($letter->canBeSatisfiedBy('', 5));
        $this->assertFalse($upper->canBeSatisfiedBy('', 5));
    }
}
