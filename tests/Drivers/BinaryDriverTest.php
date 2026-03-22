<?php

declare(strict_types=1);

namespace Tests\Drivers;

use Myerscode\Utilities\Random\Drivers\BinaryDriver;
use Tests\BaseTestSuite;

class BinaryDriverTest extends BaseTestSuite
{
    protected BinaryDriver $driver;

    protected function setUp(): void
    {
        $this->driver = new BinaryDriver();
    }

    public function testDigestContainsOnlyBinaryCharacters(): void
    {
        $seed = $this->driver->digest();
        $this->assertMatchesRegularExpression('/^[01]+$/', $seed);
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
        $this->assertMatchesRegularExpression('/^[01]+$/', $second);
    }
}
