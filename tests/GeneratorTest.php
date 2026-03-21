<?php

declare(strict_types=1);

namespace Tests;

use Myerscode\Utilities\Random\Drivers\AlphaNumericDriver;
use Myerscode\Utilities\Random\Generator;
use PHPUnit\Framework\Attributes\DataProvider;

class GeneratorTest extends BaseTestSuite
{
    private Generator $generator;

    protected function setUp(): void
    {
        $this->generator = new Generator(new AlphaNumericDriver());
    }

    public function testMakeReturnsStringOfCorrectLength(): void
    {
        $result = $this->generator->make(8);
        $this->assertSame(8, strlen($result));
    }

    public function testMakeWithMultipleChunks(): void
    {
        $result = $this->generator->make(4, 3, '-');
        $this->assertSame(14, strlen($result));
        $this->assertCount(3, explode('-', $result));
    }

    public function testMakeWithSingleChunkIgnoresSpacer(): void
    {
        $result = $this->generator->make(5, 1, '-');
        $this->assertSame(5, strlen($result));
        $this->assertStringNotContainsString('-', $result);
    }

    public function testMakeWithZeroChunksDefaultsToOne(): void
    {
        $result = $this->generator->make(5, 0);
        $this->assertSame(5, strlen($result));
    }

    public function testMakeWithNegativeChunkLengthDefaultsToOne(): void
    {
        $result = $this->generator->make(-5);
        $this->assertSame(1, strlen($result));
    }

    public function testMakeWithZeroChunkLengthDefaultsToOne(): void
    {
        $result = $this->generator->make(0);
        $this->assertSame(1, strlen($result));
    }

    public function testGetPoolReturnsString(): void
    {
        $pool = $this->generator->getPool();
        $this->assertNotEmpty($pool);
    }

    public function testSetPoolChangesPool(): void
    {
        $this->generator->setPool('ABC');
        $this->assertSame('ABC', $this->generator->getPool());

        $result = $this->generator->make(10);
        $this->assertMatchesRegularExpression('/^[ABC]+$/', $result);
    }

    /** @return array<string, array{int, int, string, int}> */
    public static function chunkConfigProvider(): array
    {
        return [
            'single chunk no spacer' => [4, 1, '', 4],
            'two chunks with dash' => [3, 2, '-', 7],
            'three chunks with space' => [2, 3, ' ', 8],
        ];
    }

    #[DataProvider('chunkConfigProvider')]
    public function testMakeWithVariousConfigurations(
        int $chunkLength,
        int $numChunks,
        string $spacer,
        int $expectedLength,
    ): void {
        $result = $this->generator->make($chunkLength, $numChunks, $spacer);
        $this->assertSame($expectedLength, strlen($result));
    }
}
