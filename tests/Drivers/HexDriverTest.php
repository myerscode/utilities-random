<?php

declare(strict_types=1);

namespace Tests\Drivers;

use Myerscode\Utilities\Random\Drivers\HexDriver;
use Tests\BaseTestSuite;

class HexDriverTest extends BaseTestSuite
{
    protected HexDriver $driver;

    protected function setUp(): void
    {
        $this->driver = new HexDriver();
    }

    public function testDigestContainsOnlyHexCharacters(): void
    {
        $seed = $this->driver->digest();
        $this->assertMatchesRegularExpression('/^[0-9a-f]+$/', $seed);
    }

    public function testDigestContainsExpectedLength(): void
    {
        $digest = $this->driver->digest();
        $this->assertSame(16, strlen($digest));
    }

    public function testSeedRegeneratesDigest(): void
    {
        $first = $this->driver->digest();
        $this->driver->seed();
        $second = $this->driver->digest();

        $this->assertSame(strlen($first), strlen($second));
        $this->assertMatchesRegularExpression('/^[0-9a-f]+$/', $second);
    }
}
