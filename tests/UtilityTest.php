<?php

declare(strict_types=1);

namespace Tests;

use Myerscode\Utilities\Random\Drivers\AlphaDriver;
use Myerscode\Utilities\Random\Drivers\AlphaNumericDriver;
use Myerscode\Utilities\Random\Drivers\NumericDriver;
use Myerscode\Utilities\Random\Exceptions\InvalidProviderException;
use Myerscode\Utilities\Random\Exceptions\UniqueThresholdReachedException;
use Myerscode\Utilities\Random\Utility;
use PHPUnit\Framework\Attributes\DataProvider;

class UtilityTest extends BaseTestSuite
{
    /** @return array<string, array{string}> */
    public static function driverClassProvider(): array
    {
        return [
            'alpha' => [AlphaDriver::class],
            'alphanumeric' => [AlphaNumericDriver::class],
            'numeric' => [NumericDriver::class],
        ];
    }

    #[DataProvider('driverClassProvider')]
    public function testConstructorWithClassName(string $driver): void
    {
        $this->assertInstanceOf(Utility::class, $this->utility($driver));
    }

    public function testConstructorWithDriverInstance(): void
    {
        $driver = new AlphaNumericDriver();
        $utility = new Utility($driver);
        $this->assertInstanceOf(Utility::class, $utility);
    }

    public function testConstructorThrowsForInvalidClass(): void
    {
        $this->expectException(InvalidProviderException::class);
        new Utility('NonExistentClass');
    }

    public function testConstructorThrowsForNonDriverClass(): void
    {
        $this->expectException(InvalidProviderException::class);
        new Utility(\stdClass::class);
    }

    public function testGenerateReturnsString(): void
    {
        $utility = new Utility(AlphaNumericDriver::class);
        $result = $utility->generate();
        $this->assertNotEmpty($result);
        $this->assertSame(5, strlen($result));
    }

    public function testLengthSetsOutputLength(): void
    {
        $utility = new Utility(AlphaNumericDriver::class);
        $result = $utility->length(10)->generate();
        $this->assertSame(10, strlen($result));
    }

    public function testChunksCreatesMultipleChunks(): void
    {
        $utility = new Utility(AlphaNumericDriver::class);
        $result = $utility->length(4)->chunks(3)->spacer('-')->generate();
        $parts = explode('-', $result);
        $this->assertCount(3, $parts);

        foreach ($parts as $part) {
            $this->assertSame(4, strlen($part));
        }
    }

    public function testSpacerSetsDelimiter(): void
    {
        $utility = new Utility(AlphaNumericDriver::class);
        $result = $utility->length(3)->chunks(2)->spacer('_')->generate();
        $this->assertStringContainsString('_', $result);
    }

    public function testUniqueReturnsDistinctValues(): void
    {
        $utility = new Utility(AlphaNumericDriver::class);
        $utility->length(20);

        $first = $utility->unique();
        $second = $utility->unique();

        $this->assertNotSame($first, $second);
    }

    public function testUniqueThrowsWhenThresholdReached(): void
    {
        $utility = new Utility(NumericDriver::class);
        $utility->length(1);

        $this->expectException(UniqueThresholdReachedException::class);

        for ($i = 0; $i < 100; $i++) {
            $utility->unique();
        }
    }

    public function testCollisionsReturnsCount(): void
    {
        $utility = new Utility(AlphaNumericDriver::class);
        $this->assertSame(0, $utility->collisions());
    }

    public function testResetClearsState(): void
    {
        $utility = new Utility(AlphaNumericDriver::class);
        $utility->length(20);
        $utility->generate();
        $utility->reset();

        $this->assertSame(0, $utility->collisions());
    }

    public function testResetReturnsSelf(): void
    {
        $utility = new Utility(AlphaNumericDriver::class);
        $this->assertSame($utility, $utility->reset());
    }

    public function testFluentInterface(): void
    {
        $utility = new Utility(AlphaNumericDriver::class);
        $result = $utility->length(4)->chunks(2)->spacer('-');
        $this->assertInstanceOf(Utility::class, $result);
    }

    public function testPermutationsReturnsExpectedValue(): void
    {
        $utility = new Utility(NumericDriver::class);
        $utility->length(3);
        $this->assertSame(50 ** 3, $utility->permutations());
    }

    public function testSeedRegeneratesDriver(): void
    {
        $utility = new Utility(AlphaNumericDriver::class);
        $utility->seed();
        $result = $utility->generate();
        $this->assertNotEmpty($result);
    }

    /** @return array<string, array{int, int}> */
    public static function lengthProvider(): array
    {
        return [
            'length 1' => [1, 1],
            'length 5' => [5, 5],
            'length 10' => [10, 10],
            'length 50' => [50, 50],
        ];
    }

    #[DataProvider('lengthProvider')]
    public function testGenerateRespectsLength(int $length, int $expected): void
    {
        $utility = new Utility(AlphaNumericDriver::class);
        $result = $utility->length($length)->generate();
        $this->assertSame($expected, strlen($result));
    }
}
