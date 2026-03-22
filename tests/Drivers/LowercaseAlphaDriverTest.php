<?php

declare(strict_types=1);

namespace Tests\Drivers;

use Myerscode\Utilities\Random\Drivers\LowercaseAlphaDriver;
use Tests\BaseTestSuite;

class LowercaseAlphaDriverTest extends BaseTestSuite
{
    protected LowercaseAlphaDriver $driver;

    protected function setUp(): void
    {
        $this->driver = new LowercaseAlphaDriver();
    }

    public function testDigestContainsOnlyLowercaseCharacters(): void
    {
        $seed = $this->driver->digest();
        $this->assertMatchesRegularExpression('/^[a-z]+$/', $seed);
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
        $this->assertMatchesRegularExpression('/^[a-z]+$/', $second);
    }
}
