<?php

declare(strict_types=1);

namespace Tests\Constraints\Pool;

use Myerscode\Utilities\Random\Constraints\Pool\ExcludeCharacters;
use Tests\BaseTestSuite;

final class ExcludeCharactersTest extends BaseTestSuite
{
    public function testRemovesSpecifiedCharacters(): void
    {
        $excludeCharacters = new ExcludeCharacters(['a', 'b', 'c']);
        $this->assertSame('defgh', $excludeCharacters->filter('abcdefgh'));
    }

    public function testLeavesPoolIntactWhenNoMatchingCharacters(): void
    {
        $excludeCharacters = new ExcludeCharacters(['x', 'y', 'z']);
        $this->assertSame('abcdef', $excludeCharacters->filter('abcdef'));
    }

    public function testHandlesEmptyExclusionList(): void
    {
        $excludeCharacters = new ExcludeCharacters([]);
        $this->assertSame('abcdef', $excludeCharacters->filter('abcdef'));
    }

    public function testHandlesEmptyPool(): void
    {
        $excludeCharacters = new ExcludeCharacters(['a']);
        $this->assertSame('', $excludeCharacters->filter(''));
    }

    public function testRemovesAllWhenPoolMatchesExclusions(): void
    {
        $excludeCharacters = new ExcludeCharacters(['a', 'b']);
        $this->assertSame('', $excludeCharacters->filter('aabb'));
    }

    public function testIsCaseSensitive(): void
    {
        $excludeCharacters = new ExcludeCharacters(['a']);
        $this->assertSame('AbcA', $excludeCharacters->filter('AabcA'));
    }
}
