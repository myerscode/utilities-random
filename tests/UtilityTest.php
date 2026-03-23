<?php

declare(strict_types=1);

namespace Tests;

use Myerscode\Utilities\Random\Constraints\Output\NoRepeatingCharacters;
use Myerscode\Utilities\Random\Constraints\Pool\ExcludeSimilarCharacters;
use Myerscode\Utilities\Random\Drivers\AlphaDriver;
use Myerscode\Utilities\Random\Drivers\AlphaNumericDriver;
use Myerscode\Utilities\Random\Drivers\NumericDriver;
use Myerscode\Utilities\Random\Exceptions\InvalidConstraintException;
use Myerscode\Utilities\Random\Exceptions\InvalidProviderException;
use Myerscode\Utilities\Random\Exceptions\UniqueThresholdReachedException;
use Myerscode\Utilities\Random\Utility;
use PHPUnit\Framework\Attributes\DataProvider;
use stdClass;

final class UtilityTest extends BaseTestSuite
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

    public function testCollisionsReturnsCount(): void
    {
        $utility = new Utility(AlphaNumericDriver::class);
        $this->assertSame(0, $utility->collisions());
    }

    public function testConstraintsAcceptsClassNames(): void
    {
        $utility = new Utility(AlphaNumericDriver::class);
        $result = $utility->constraints([ExcludeSimilarCharacters::class]);
        $this->assertInstanceOf(Utility::class, $result);
    }

    public function testConstraintsAcceptsInstances(): void
    {
        $utility = new Utility(AlphaNumericDriver::class);
        $result = $utility->constraints([new NoRepeatingCharacters()]);
        $this->assertInstanceOf(Utility::class, $result);
    }

    public function testConstraintsAcceptsMixedClassNamesAndInstances(): void
    {
        $utility = new Utility(AlphaNumericDriver::class);
        $result = $utility->constraints([
            ExcludeSimilarCharacters::class,
            new NoRepeatingCharacters(),
        ]);
        $this->assertInstanceOf(Utility::class, $result);
    }

    public function testConstraintsReturnsSelfForFluentChaining(): void
    {
        $utility = new Utility(AlphaNumericDriver::class);
        $result = $utility->length(10)->constraints([NoRepeatingCharacters::class])->chunks(2)->spacer('-');
        $this->assertInstanceOf(Utility::class, $result);
    }

    public function testConstraintsThrowsForNonConstraintClass(): void
    {
        $utility = new Utility(AlphaNumericDriver::class);
        $this->expectException(InvalidConstraintException::class);
        $utility->constraints([stdClass::class]);
    }

    public function testConstraintsThrowsForNonExistentClass(): void
    {
        $utility = new Utility(AlphaNumericDriver::class);
        $this->expectException(InvalidConstraintException::class);
        $utility->constraints(['NonExistentConstraintClass']);
    }

    public function testConstructorThrowsForInvalidClass(): void
    {
        $this->expectException(InvalidProviderException::class);
        new Utility('NonExistentClass');
    }

    public function testConstructorThrowsForNonDriverClass(): void
    {
        $this->expectException(InvalidProviderException::class);
        new Utility(stdClass::class);
    }

    #[DataProvider('driverClassProvider')]
    public function testConstructorWithClassName(string $driver): void
    {
        $this->assertInstanceOf(Utility::class, $this->utility($driver));
    }

    public function testConstructorWithDriverInstance(): void
    {
        $alphaNumericDriver = new AlphaNumericDriver();
        $utility = new Utility($alphaNumericDriver);
        $this->assertInstanceOf(Utility::class, $utility);
    }

    public function testFluentInterface(): void
    {
        $utility = new Utility(AlphaNumericDriver::class);
        $result = $utility->length(4)->chunks(2)->spacer('-');
        $this->assertInstanceOf(Utility::class, $result);
    }

    #[DataProvider('lengthProvider')]
    public function testGenerateRespectsLength(int $length, int $expected): void
    {
        $utility = new Utility(AlphaNumericDriver::class);
        $result = $utility->length($length)->generate();
        $this->assertSame($expected, strlen($result));
    }

    public function testGenerateReturnsString(): void
    {
        $utility = new Utility(AlphaNumericDriver::class);
        $result = $utility->generate();
        $this->assertNotEmpty($result);
        $this->assertSame(5, strlen($result));
    }

    public function testGenerateWithCombinedConstraints(): void
    {
        $utility = new Utility(AlphaNumericDriver::class);
        $utility->constraints([
            ExcludeSimilarCharacters::class,
            NoRepeatingCharacters::class,
        ])->length(10);

        $result = $utility->generate();
        $this->assertDoesNotMatchRegularExpression('/[oO0I1l]/', $result);

        for ($i = 1; $i < strlen($result); $i++) {
            $this->assertNotSame($result[$i], $result[$i - 1]);
        }
    }

    public function testGenerateWithExcludeSimilarCharactersConstraint(): void
    {
        $utility = new Utility(AlphaNumericDriver::class);
        $utility->constraints([ExcludeSimilarCharacters::class])->length(50);

        $result = $utility->generate();
        $this->assertDoesNotMatchRegularExpression('/[oO0I1l]/', $result);
    }

    public function testGenerateWithNoRepeatingCharactersConstraint(): void
    {
        $utility = new Utility(AlphaNumericDriver::class);
        $utility->constraints([NoRepeatingCharacters::class])->length(10);

        for ($i = 0; $i < 10; $i++) {
            $result = $utility->generate();

            for ($j = 1; $j < strlen($result); $j++) {
                $this->assertNotSame($result[$j], $result[$j - 1]);
            }
        }
    }

    public function testLengthSetsOutputLength(): void
    {
        $utility = new Utility(AlphaNumericDriver::class);
        $result = $utility->length(10)->generate();
        $this->assertSame(10, strlen($result));
    }

    public function testPermutationsReturnsExpectedValue(): void
    {
        $utility = new Utility(NumericDriver::class);
        $utility->length(3);
        $this->assertSame(50 ** 3, $utility->permutations());
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

    public function testSeedRegeneratesDriver(): void
    {
        $utility = new Utility(AlphaNumericDriver::class);
        $utility->seed();

        $result = $utility->generate();
        $this->assertNotEmpty($result);
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
}
