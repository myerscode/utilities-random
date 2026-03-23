<?php

declare(strict_types=1);

namespace Tests\Constraints\Output;

use Myerscode\Utilities\Random\Constraints\Output\NoRepeatingCharacters;
use Tests\BaseTestSuite;

final class NoRepeatingCharactersTest extends BaseTestSuite
{
    private NoRepeatingCharacters $noRepeatingCharacters;

    protected function setUp(): void
    {
        $this->noRepeatingCharacters = new NoRepeatingCharacters();
    }

    public function testCanBeSatisfiedByFailsWithSingleChar(): void
    {
        $this->assertFalse($this->noRepeatingCharacters->canBeSatisfiedBy('AAAA', 5));
    }

    public function testCanBeSatisfiedByPassesWithSingleCharPoolAndLengthOne(): void
    {
        $this->assertTrue($this->noRepeatingCharacters->canBeSatisfiedBy('AAAA', 1));
    }

    public function testCanBeSatisfiedByWithMultipleDistinctChars(): void
    {
        $this->assertTrue($this->noRepeatingCharacters->canBeSatisfiedBy('abcdef', 5));
    }

    public function testCanBeSatisfiedByWithTwoDistinctChars(): void
    {
        $this->assertTrue($this->noRepeatingCharacters->canBeSatisfiedBy('ababab', 5));
    }

    public function testFailsWithConsecutiveRepeats(): void
    {
        $this->assertFalse($this->noRepeatingCharacters->passes('aabcde'));
    }

    public function testFailsWithRepeatsAtEnd(): void
    {
        $this->assertFalse($this->noRepeatingCharacters->passes('abcdee'));
    }

    public function testFailsWithRepeatsInMiddle(): void
    {
        $this->assertFalse($this->noRepeatingCharacters->passes('abccde'));
    }

    public function testPassesWithAlternatingCharacters(): void
    {
        $this->assertTrue($this->noRepeatingCharacters->passes('ababab'));
    }

    public function testPassesWithEmptyString(): void
    {
        $this->assertTrue($this->noRepeatingCharacters->passes(''));
    }

    public function testPassesWithNoRepeats(): void
    {
        $this->assertTrue($this->noRepeatingCharacters->passes('abcdef'));
    }

    public function testPassesWithSingleCharacter(): void
    {
        $this->assertTrue($this->noRepeatingCharacters->passes('a'));
    }
}
