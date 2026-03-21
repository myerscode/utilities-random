<?php

declare(strict_types=1);

namespace Tests\Drivers;

use Myerscode\Utilities\Random\Drivers\AlphaNumericDriver;
use Tests\BaseTestSuite;

class AlphaNumericDriverTest extends BaseTestSuite
{
    protected AlphaNumericDriver $driver;

    protected function setUp(): void
    {
        $this->driver = new AlphaNumericDriver();
    }

    public function testDigestContainsOnlyAlphaNumericCharacters(): void
    {
        $seed = $this->driver->digest();
        $this->assertMatchesRegularExpression('/^[a-zA-Z0-9]+$/', $seed);
    }

    public function testDigestContainsExpectedLength(): void
    {
        $digest = $this->driver->digest();
        $this->assertSame(62, strlen($digest));
    }

    public function testSeedRegeneratesDigest(): void
    {
        $first = $this->driver->digest();
        $this->driver->seed();
        $second = $this->driver->digest();

        $this->assertSame(strlen($first), strlen($second));
        $this->assertMatchesRegularExpression('/^[a-zA-Z0-9]+$/', $second);
    }
}
