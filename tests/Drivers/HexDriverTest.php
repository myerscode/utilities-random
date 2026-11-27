<?php

declare(strict_types=1);

namespace Tests\Drivers;

use Myerscode\Utilities\Random\Drivers\HexDriver;
use Tests\BaseTestSuite;

final class HexDriverTest extends BaseTestSuite
{
    private HexDriver $hexDriver;

    protected function setUp(): void
    {
        $this->hexDriver = new HexDriver();
    }

    public function testDigestContainsExpectedLength(): void
    {
        $digest = $this->hexDriver->digest();
        $this->assertSame(16, strlen($digest));
    }

    public function testDigestContainsOnlyHexCharacters(): void
    {
        $seed = $this->hexDriver->digest();
        $this->assertMatchesRegularExpression('/^[0-9a-f]+$/', $seed);
    }

    public function testSeedRegeneratesDigest(): void
    {
        $first = $this->hexDriver->digest();
        $this->hexDriver->seed();
        $second = $this->hexDriver->digest();

        $this->assertSame(strlen($first), strlen($second));
        $this->assertMatchesRegularExpression('/^[0-9a-f]+$/', $second);
    }

    public function testDigestHasExpectedCharacterMultiset(): void
    {
        $expected = str_split(implode('', range(0, 9)) . implode('', range('a', 'f')));
        $actual = str_split($this->hexDriver->digest());
        sort($expected);
        sort($actual);

        $this->assertSame($expected, $actual);
    }
}
