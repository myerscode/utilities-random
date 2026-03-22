<?php

declare(strict_types=1);

namespace Tests\Drivers;

use Myerscode\Utilities\Random\Drivers\UppercaseAlphaDriver;
use Tests\BaseTestSuite;

class UppercaseAlphaDriverTest extends BaseTestSuite
{
    protected UppercaseAlphaDriver $driver;

    protected function setUp(): void
    {
        $this->driver = new UppercaseAlphaDriver();
    }

    public function testDigestContainsOnlyUppercaseCharacters(): void
    {
        $seed = $this->driver->digest();
        $this->assertMatchesRegularExpression('/^[A-Z]+$/', $seed);
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
        $this->assertMatchesRegularExpression('/^[A-Z]+$/', $second);
    }
}
