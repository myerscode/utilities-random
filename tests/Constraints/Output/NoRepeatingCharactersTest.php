<?php

declare(strict_types=1);

namespace Tests\Constraints\Output;

use Myerscode\Utilities\Random\Constraints\Output\NoRepeatingCharacters;
use Tests\BaseTestSuite;

class NoRepeatingCharactersTest extends BaseTestSuite
{
    private NoRepeatingCharacters $rule;

    protected function setUp(): void
    {
        $this->rule = new NoRepeatingCharacters();
    }

    public function testPassesWithNoRepeats(): void
    {
        $this->assertTrue($this->rule->passes('abcdef'));
    }

    public function testFailsWithConsecutiveRepeats(): void
    {
        $this->assertFalse($this->rule->passes('aabcde'));
    }

    public function testFailsWithRepeatsInMiddle(): void
    {
        $this->assertFalse($this->rule->passes('abccde'));
    }

    public function testFailsWithRepeatsAtEnd(): void
    {
        $this->assertFalse($this->rule->passes('abcdee'));
    }

    public function testPassesWithSingleCharacter(): void
    {
        $this->assertTrue($this->rule->passes('a'));
    }

    public function testPassesWithEmptyString(): void
    {
        $this->assertTrue($this->rule->passes(''));
    }

    public function testPassesWithAlternatingCharacters(): void
    {
        $this->assertTrue($this->rule->passes('ababab'));
    }

    public function testCanBeSatisfiedByWithMultipleDistinctChars(): void
    {
        $this->assertTrue($this->rule->canBeSatisfiedBy('abcdef', 5));
    }

    public function testCanBeSatisfiedByFailsWithSingleChar(): void
    {
        $this->assertFalse($this->rule->canBeSatisfiedBy('AAAA', 5));
    }

    public function testCanBeSatisfiedByWithTwoDistinctChars(): void
    {
        $this->assertTrue($this->rule->canBeSatisfiedBy('ababab', 5));
    }

    public function testCanBeSatisfiedByPassesWithSingleCharPoolAndLengthOne(): void
    {
        $this->assertTrue($this->rule->canBeSatisfiedBy('AAAA', 1));
    }
}
