<?php

declare(strict_types=1);

namespace Tests\Rules;

use Myerscode\Utilities\Random\Rules\NoRepeatingCharacters;
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
}
