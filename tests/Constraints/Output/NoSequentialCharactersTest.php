<?php

declare(strict_types=1);

namespace Tests\Constraints\Output;

use Myerscode\Utilities\Random\Constraints\Output\NoSequentialCharacters;
use Tests\BaseTestSuite;

final class NoSequentialCharactersTest extends BaseTestSuite
{
    public function testFailsWithAscendingLetters(): void
    {
        $noSequentialCharacters = new NoSequentialCharacters();
        $this->assertFalse($noSequentialCharacters->passes('xabcx'));
    }

    public function testFailsWithDescendingLetters(): void
    {
        $noSequentialCharacters = new NoSequentialCharacters();
        $this->assertFalse($noSequentialCharacters->passes('xcbax'));
    }

    public function testFailsWithAscendingDigits(): void
    {
        $noSequentialCharacters = new NoSequentialCharacters();
        $this->assertFalse($noSequentialCharacters->passes('x123x'));
    }

    public function testFailsWithDescendingDigits(): void
    {
        $noSequentialCharacters = new NoSequentialCharacters();
        $this->assertFalse($noSequentialCharacters->passes('x321x'));
    }

    public function testPassesWithNonSequentialCharacters(): void
    {
        $noSequentialCharacters = new NoSequentialCharacters();
        $this->assertTrue($noSequentialCharacters->passes('axzm'));
    }

    public function testPassesWithRepeatingCharacters(): void
    {
        $noSequentialCharacters = new NoSequentialCharacters();
        $this->assertTrue($noSequentialCharacters->passes('aaa'));
    }

    public function testPassesWithShortString(): void
    {
        $noSequentialCharacters = new NoSequentialCharacters();
        $this->assertTrue($noSequentialCharacters->passes('ab'));
    }

    public function testPassesWithEmptyString(): void
    {
        $noSequentialCharacters = new NoSequentialCharacters();
        $this->assertTrue($noSequentialCharacters->passes(''));
    }

    public function testCustomLengthDetectsLongerSequences(): void
    {
        $noSequentialCharacters = new NoSequentialCharacters(4);
        $this->assertTrue($noSequentialCharacters->passes('xabcx'));
        $this->assertFalse($noSequentialCharacters->passes('xabcdx'));
    }

    public function testCustomLengthOfTwoIsSensitive(): void
    {
        $noSequentialCharacters = new NoSequentialCharacters(2);
        $this->assertFalse($noSequentialCharacters->passes('xabx'));
        $this->assertTrue($noSequentialCharacters->passes('xacx'));
    }

    public function testSequenceAtStartOfString(): void
    {
        $noSequentialCharacters = new NoSequentialCharacters();
        $this->assertFalse($noSequentialCharacters->passes('abcxyz'));
    }

    public function testSequenceAtEndOfString(): void
    {
        $noSequentialCharacters = new NoSequentialCharacters();
        $this->assertFalse($noSequentialCharacters->passes('xyzabc'));
    }
}
