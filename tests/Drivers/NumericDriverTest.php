<?php

declare(strict_types=1);

namespace Tests\Drivers;

use Myerscode\Utilities\Random\Drivers\NumericDriver;
use Tests\BaseTestSuite;

final class NumericDriverTest extends BaseTestSuite
{
    private NumericDriver $numericDriver;

    protected function setUp(): void
    {
        $this->numericDriver = new NumericDriver();
    }

    public function testDigestContainsExpectedLength(): void
    {
        $digest = $this->numericDriver->digest();
        $this->assertSame(50, strlen($digest));
    }

    public function testDigestContainsOnlyNumericCharacters(): void
    {
        $seed = $this->numericDriver->digest();
        $this->assertMatchesRegularExpression('/^\d+$/', $seed);
    }

    public function testDigestHasExpectedCharacterMultiset(): void
    {
        $expected = str_split(str_repeat(implode('', range(0, 9)), 5));
        $actual = str_split($this->numericDriver->digest());
        sort($expected);
        sort($actual);

        $this->assertSame($expected, $actual);
    }

    public function testSeedRegeneratesDigest(): void
    {
        $first = $this->numericDriver->digest();
        $this->numericDriver->seed();
        $second = $this->numericDriver->digest();

        $this->assertSame(strlen($first), strlen($second));
        $this->assertMatchesRegularExpression('/^\d+$/', $second);
    }
}
