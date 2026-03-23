<?php

declare(strict_types=1);

namespace Tests\Constraints\Output;

use Myerscode\Utilities\Random\Constraints\Output\MustContainDigit;
use Myerscode\Utilities\Random\Constraints\Output\MustContainLetter;
use Myerscode\Utilities\Random\Constraints\Output\MustContainUppercase;
use Tests\BaseTestSuite;

final class MustContainPatternTest extends BaseTestSuite
{
    public function testCanBeSatisfiedByReturnsFalseWhenPoolLacksMatch(): void
    {
        $mustContainDigit = new MustContainDigit();
        $this->assertFalse($mustContainDigit->canBeSatisfiedBy('abcdef', 5));
    }
    public function testCanBeSatisfiedByReturnsTrueWhenPoolContainsMatch(): void
    {
        $mustContainDigit = new MustContainDigit();
        $this->assertTrue($mustContainDigit->canBeSatisfiedBy('abc123', 5));
    }

    public function testCanBeSatisfiedByWithEmptyPool(): void
    {
        $mustContainDigit = new MustContainDigit();
        $mustContainLetter = new MustContainLetter();
        $mustContainUppercase = new MustContainUppercase();

        $this->assertFalse($mustContainDigit->canBeSatisfiedBy('', 5));
        $this->assertFalse($mustContainLetter->canBeSatisfiedBy('', 5));
        $this->assertFalse($mustContainUppercase->canBeSatisfiedBy('', 5));
    }

    public function testMustContainLetterCanBeSatisfied(): void
    {
        $mustContainLetter = new MustContainLetter();
        $this->assertTrue($mustContainLetter->canBeSatisfiedBy('123a', 5));
        $this->assertFalse($mustContainLetter->canBeSatisfiedBy('12345', 5));
    }

    public function testMustContainUppercaseCanBeSatisfied(): void
    {
        $mustContainUppercase = new MustContainUppercase();
        $this->assertTrue($mustContainUppercase->canBeSatisfiedBy('abcA', 5));
        $this->assertFalse($mustContainUppercase->canBeSatisfiedBy('abcdef', 5));
    }
}
