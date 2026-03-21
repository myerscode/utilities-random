<?php

declare(strict_types=1);

namespace Tests\Drivers;

use Myerscode\Utilities\Random\Drivers\AlphaDriver;
use Tests\BaseTestSuite;

class AlphaDriverTest extends BaseTestSuite
{
    protected AlphaDriver $driver;

    protected function setUp(): void
    {
        $this->driver = new AlphaDriver();
    }

    public function testDigestContainsOnlyAlphaCharacters(): void
    {
        $seed = $this->driver->digest();
        $this->assertMatchesRegularExpression('/^[a-zA-Z]+$/', $seed);
    }

    public function testDigestContainsExpectedLength(): void
    {
        $digest = $this->driver->digest();
        $this->assertSame(52, strlen($digest));
    }

    public function testSeedRegeneratesDigest(): void
    {
        $first = $this->driver->digest();
        $this->driver->seed();
        $second = $this->driver->digest();

        $this->assertSame(strlen($first), strlen($second));
        $this->assertMatchesRegularExpression('/^[a-zA-Z]+$/', $second);
    }
}
