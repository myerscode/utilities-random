<?php

declare(strict_types=1);

namespace Tests;

use Myerscode\Utilities\Random\Constraints\Output\MustContainDigit;
use Myerscode\Utilities\Random\Constraints\Output\MustContainLetter;
use Myerscode\Utilities\Random\Constraints\Output\MustContainUppercase;
use Myerscode\Utilities\Random\Constraints\Output\NoRepeatingCharacters;
use Myerscode\Utilities\Random\Constraints\Output\RegexConstraint;
use Myerscode\Utilities\Random\Constraints\Pool\ExcludeCharacters;
use Myerscode\Utilities\Random\Constraints\Pool\ExcludeSimilarCharacters;
use Myerscode\Utilities\Random\Drivers\AlphaNumericDriver;
use Myerscode\Utilities\Random\Drivers\NumericDriver;
use Myerscode\Utilities\Random\Exceptions\EmptyPoolException;
use Myerscode\Utilities\Random\Exceptions\UnsatisfiableConstraintException;
use Myerscode\Utilities\Random\Exceptions\ValidationThresholdReachedException;
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

    public function testSetConstraintsAppliesPoolConstraint(): void
    {
        $this->generator->setConstraints([new ExcludeSimilarCharacters()]);
        $pool = $this->generator->getPool();

        $this->assertStringNotContainsString('o', $pool);
        $this->assertStringNotContainsString('O', $pool);
        $this->assertStringNotContainsString('0', $pool);
        $this->assertStringNotContainsString('I', $pool);
        $this->assertStringNotContainsString('1', $pool);
        $this->assertStringNotContainsString('l', $pool);
    }

    public function testSetConstraintsAppliesOutputConstraint(): void
    {
        $this->generator->setPool('AB');
        $this->generator->setConstraints([new NoRepeatingCharacters()]);

        for ($i = 0; $i < 20; $i++) {
            $result = $this->generator->make(4);

            for ($j = 1; $j < strlen($result); $j++) {
                $this->assertNotSame($result[$j], $result[$j - 1]);
            }
        }
    }

    public function testSetConstraintsWithBothTypes(): void
    {
        $this->generator->setConstraints([
            new ExcludeSimilarCharacters(),
            new NoRepeatingCharacters(),
        ]);

        $pool = $this->generator->getPool();
        $this->assertStringNotContainsString('0', $pool);

        $result = $this->generator->make(6);
        for ($i = 1; $i < strlen($result); $i++) {
            $this->assertNotSame($result[$i], $result[$i - 1]);
        }
    }

    public function testGetConstraintsReturnsSetConstraints(): void
    {
        $constraints = [new ExcludeSimilarCharacters(), new NoRepeatingCharacters()];
        $this->generator->setConstraints($constraints);

        $this->assertCount(2, $this->generator->getConstraints());
    }

    public function testSetConstraintsClearsExisting(): void
    {
        $this->generator->setConstraints([new ExcludeSimilarCharacters()]);
        $this->generator->setConstraints([]);

        $this->assertCount(0, $this->generator->getConstraints());
    }

    public function testValidationThresholdThrowsException(): void
    {
        $this->generator->setConstraints([new RegexConstraint('/^[0-9]+$/')]);
        $this->generator->setPool('ABC');

        $this->expectException(ValidationThresholdReachedException::class);
        $this->generator->make(2);
    }

    public function testSetPoolThrowsWhenEmpty(): void
    {
        $this->expectException(EmptyPoolException::class);
        $this->generator->setPool('');
    }

    public function testSetConstraintsThrowsWhenPoolConstraintsEmptyThePool(): void
    {
        $this->generator->setPool('abc');

        $this->expectException(EmptyPoolException::class);
        $this->generator->setConstraints([new ExcludeCharacters(['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9'])]);
    }

    public function testMakeThrowsWhenOutputConstraintCannotBeSatisfied(): void
    {
        $generator = new Generator(new NumericDriver());
        $generator->setConstraints([new MustContainLetter()]);

        $this->expectException(UnsatisfiableConstraintException::class);
        $generator->make(5);
    }

    public function testMakeThrowsForMustContainUppercaseWithNumericPool(): void
    {
        $generator = new Generator(new NumericDriver());
        $generator->setConstraints([new MustContainUppercase()]);

        $this->expectException(UnsatisfiableConstraintException::class);
        $generator->make(5);
    }

    public function testMakeThrowsForMustContainDigitWithAlphaPool(): void
    {
        $this->generator->setConstraints([new ExcludeCharacters(['0', '1', '2', '3', '4', '5', '6', '7', '8', '9']), new MustContainDigit()]);

        $this->expectException(UnsatisfiableConstraintException::class);
        $this->generator->make(5);
    }

    public function testMakeThrowsForNoRepeatingWithSingleCharPoolAndLengthAboveOne(): void
    {
        $this->generator->setConstraints([new ExcludeCharacters(array_merge(range('a', 'z'), range('A', 'Z'), range('1', '9'))), new NoRepeatingCharacters()]);

        $this->expectException(UnsatisfiableConstraintException::class);
        $this->generator->make(2);
    }

    public function testMakeAllowsNoRepeatingWithSingleCharPoolAndLengthOne(): void
    {
        $this->generator->setConstraints([new ExcludeCharacters(array_merge(range('a', 'z'), range('A', 'Z'), range('1', '9'))), new NoRepeatingCharacters()]);

        $result = $this->generator->make(1);
        $this->assertSame(1, strlen($result));
    }
}
