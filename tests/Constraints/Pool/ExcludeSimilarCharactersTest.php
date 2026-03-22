<?php

declare(strict_types=1);

namespace Tests\Constraints\Pool;

use Myerscode\Utilities\Random\Constraints\Pool\ExcludeSimilarCharacters;
use Tests\BaseTestSuite;

class ExcludeSimilarCharactersTest extends BaseTestSuite
{
    private ExcludeSimilarCharacters $rule;

    protected function setUp(): void
    {
        $this->rule = new ExcludeSimilarCharacters();
    }

    public function testRemovesSimilarCharactersFromPool(): void
    {
        $filtered = $this->rule->filter('abcOo0I1lxyz');
        $this->assertSame('abcxyz', $filtered);
    }

    public function testLeavesPoolIntactWhenNoSimilarCharacters(): void
    {
        $filtered = $this->rule->filter('abcdef');
        $this->assertSame('abcdef', $filtered);
    }

    public function testHandlesEmptyPool(): void
    {
        $filtered = $this->rule->filter('');
        $this->assertSame('', $filtered);
    }

    public function testRemovesAllWhenPoolIsOnlySimilarCharacters(): void
    {
        $filtered = $this->rule->filter('oO0I1l');
        $this->assertSame('', $filtered);
    }
}
