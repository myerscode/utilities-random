<?php

declare(strict_types=1);

namespace Tests\Constraints\Pool;

use Myerscode\Utilities\Random\Constraints\Pool\ExcludeSimilarCharacters;
use Tests\BaseTestSuite;

final class ExcludeSimilarCharactersTest extends BaseTestSuite
{
    private ExcludeSimilarCharacters $excludeSimilarCharacters;

    protected function setUp(): void
    {
        $this->excludeSimilarCharacters = new ExcludeSimilarCharacters();
    }

    public function testRemovesSimilarCharactersFromPool(): void
    {
        $filtered = $this->excludeSimilarCharacters->filter('abcOo0I1lxyz');
        $this->assertSame('abcxyz', $filtered);
    }

    public function testLeavesPoolIntactWhenNoSimilarCharacters(): void
    {
        $filtered = $this->excludeSimilarCharacters->filter('abcdef');
        $this->assertSame('abcdef', $filtered);
    }

    public function testHandlesEmptyPool(): void
    {
        $filtered = $this->excludeSimilarCharacters->filter('');
        $this->assertSame('', $filtered);
    }

    public function testRemovesAllWhenPoolIsOnlySimilarCharacters(): void
    {
        $filtered = $this->excludeSimilarCharacters->filter('oO0I1l');
        $this->assertSame('', $filtered);
    }
}
