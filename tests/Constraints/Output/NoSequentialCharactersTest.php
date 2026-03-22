<?php

declare(strict_types=1);

namespace Tests\Constraints\Output;

use Myerscode\Utilities\Random\Constraints\Output\NoSequentialCharacters;
use Tests\BaseTestSuite;

class NoSequentialCharactersTest extends BaseTestSuite
{
    public function testFailsWithAscendingLetters(): void
    {
        $rule = new NoSequentialCharacters();
        $this->assertFalse($rule->passes('xabcx'));
    }

    public function testFailsWithDescendingLetters(): void
    {
        $rule = new NoSequentialCharacters();
        $this->assertFalse($rule->passes('xcbax'));
    }

    public function testFailsWithAscendingDigits(): void
    {
        $rule = new NoSequentialCharacters();
        $this->assertFalse($rule->passes('x123x'));
    }

    public function testFailsWithDescendingDigits(): void
    {
        $rule = new NoSequentialCharacters();
        $this->assertFalse($rule->passes('x321x'));
    }

    public function testPassesWithNonSequentialCharacters(): void
    {
        $rule = new NoSequentialCharacters();
        $this->assertTrue($rule->passes('axzm'));
    }

    public function testPassesWithRepeatingCharacters(): void
    {
        $rule = new NoSequentialCharacters();
        $this->assertTrue($rule->passes('aaa'));
    }

    public function testPassesWithShortString(): void
    {
        $rule = new NoSequentialCharacters();
        $this->assertTrue($rule->passes('ab'));
    }

    public function testPassesWithEmptyString(): void
    {
        $rule = new NoSequentialCharacters();
        $this->assertTrue($rule->passes(''));
    }

    public function testCustomLengthDetectsLongerSequences(): void
    {
        $rule = new NoSequentialCharacters(4);
        $this->assertTrue($rule->passes('xabcx'));
        $this->assertFalse($rule->passes('xabcdx'));
    }

    public function testCustomLengthOfTwoIsSensitive(): void
    {
        $rule = new NoSequentialCharacters(2);
        $this->assertFalse($rule->passes('xabx'));
        $this->assertTrue($rule->passes('xacx'));
    }

    public function testSequenceAtStartOfString(): void
    {
        $rule = new NoSequentialCharacters();
        $this->assertFalse($rule->passes('abcxyz'));
    }

    public function testSequenceAtEndOfString(): void
    {
        $rule = new NoSequentialCharacters();
        $this->assertFalse($rule->passes('xyzabc'));
    }
}
