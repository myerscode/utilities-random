<?php

declare(strict_types=1);

namespace Tests\Drivers;

use Myerscode\Utilities\Random\Drivers\NumericDriver;
use Tests\BaseTestSuite;

class NumericDriverTest extends BaseTestSuite
{
    protected NumericDriver $driver;

    protected function setUp(): void
    {
        $this->driver = new NumericDriver();
    }

    public function testDigestContainsOnlyNumericCharacters(): void
    {
        $seed = $this->driver->digest();
        $this->assertMatchesRegularExpression('/^\d+$/', $seed);
    }

    public function testDigestContainsExpectedLength(): void
    {
        $digest = $this->driver->digest();
        $this->assertSame(50, strlen($digest));
    }

    public function testSeedRegeneratesDigest(): void
    {
        $first = $this->driver->digest();
        $this->driver->seed();
        $second = $this->driver->digest();

        $this->assertSame(strlen($first), strlen($second));
        $this->assertMatchesRegularExpression('/^\d+$/', $second);
    }
}
