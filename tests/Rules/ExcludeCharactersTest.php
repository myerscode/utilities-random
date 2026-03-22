<?php

declare(strict_types=1);

namespace Tests\Rules;

use Myerscode\Utilities\Random\Rules\ExcludeCharacters;
use Tests\BaseTestSuite;

class ExcludeCharactersTest extends BaseTestSuite
{
    public function testRemovesSpecifiedCharacters(): void
    {
        $rule = new ExcludeCharacters(['a', 'b', 'c']);
        $this->assertSame('defgh', $rule->filter('abcdefgh'));
    }

    public function testLeavesPoolIntactWhenNoMatchingCharacters(): void
    {
        $rule = new ExcludeCharacters(['x', 'y', 'z']);
        $this->assertSame('abcdef', $rule->filter('abcdef'));
    }

    public function testHandlesEmptyExclusionList(): void
    {
        $rule = new ExcludeCharacters([]);
        $this->assertSame('abcdef', $rule->filter('abcdef'));
    }

    public function testHandlesEmptyPool(): void
    {
        $rule = new ExcludeCharacters(['a']);
        $this->assertSame('', $rule->filter(''));
    }

    public function testRemovesAllWhenPoolMatchesExclusions(): void
    {
        $rule = new ExcludeCharacters(['a', 'b']);
        $this->assertSame('', $rule->filter('aabb'));
    }

    public function testIsCaseSensitive(): void
    {
        $rule = new ExcludeCharacters(['a']);
        $this->assertSame('AbcA', $rule->filter('AabcA'));
    }
}
