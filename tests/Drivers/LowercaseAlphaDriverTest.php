<?php

declare(strict_types=1);

namespace Tests\Drivers;

use Myerscode\Utilities\Random\Drivers\LowercaseAlphaDriver;
use Tests\BaseTestSuite;

final class LowercaseAlphaDriverTest extends BaseTestSuite
{
    private LowercaseAlphaDriver $lowercaseAlphaDriver;

    protected function setUp(): void
    {
        $this->lowercaseAlphaDriver = new LowercaseAlphaDriver();
    }

    public function testDigestContainsOnlyLowercaseCharacters(): void
    {
        $seed = $this->lowercaseAlphaDriver->digest();
        $this->assertMatchesRegularExpression('/^[a-z]+$/', $seed);
    }

    public function testDigestContainsExpectedLength(): void
    {
        $digest = $this->lowercaseAlphaDriver->digest();
        $this->assertSame(52, strlen($digest));
    }

    public function testSeedRegeneratesDigest(): void
    {
        $first = $this->lowercaseAlphaDriver->digest();
        $this->lowercaseAlphaDriver->seed();
        $second = $this->lowercaseAlphaDriver->digest();

        $this->assertSame(strlen($first), strlen($second));
        $this->assertMatchesRegularExpression('/^[a-z]+$/', $second);
    }
}
