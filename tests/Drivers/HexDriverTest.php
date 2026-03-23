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

    public function testDigestContainsOnlyHexCharacters(): void
    {
        $seed = $this->hexDriver->digest();
        $this->assertMatchesRegularExpression('/^[0-9a-f]+$/', $seed);
    }

    public function testDigestContainsExpectedLength(): void
    {
        $digest = $this->hexDriver->digest();
        $this->assertSame(16, strlen($digest));
    }

    public function testSeedRegeneratesDigest(): void
    {
        $first = $this->hexDriver->digest();
        $this->hexDriver->seed();
        $second = $this->hexDriver->digest();

        $this->assertSame(strlen($first), strlen($second));
        $this->assertMatchesRegularExpression('/^[0-9a-f]+$/', $second);
    }
}
