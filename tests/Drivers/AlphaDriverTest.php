<?php

declare(strict_types=1);

namespace Tests\Drivers;

use Myerscode\Utilities\Random\Drivers\AlphaDriver;
use Tests\BaseTestSuite;

final class AlphaDriverTest extends BaseTestSuite
{
    private AlphaDriver $alphaDriver;

    protected function setUp(): void
    {
        $this->alphaDriver = new AlphaDriver();
    }

    public function testDigestContainsExpectedLength(): void
    {
        $digest = $this->alphaDriver->digest();
        $this->assertSame(52, strlen($digest));
    }

    public function testDigestContainsOnlyAlphaCharacters(): void
    {
        $seed = $this->alphaDriver->digest();
        $this->assertMatchesRegularExpression('/^[a-zA-Z]+$/', $seed);
    }

    public function testSeedRegeneratesDigest(): void
    {
        $first = $this->alphaDriver->digest();
        $this->alphaDriver->seed();
        $second = $this->alphaDriver->digest();

        $this->assertSame(strlen($first), strlen($second));
        $this->assertMatchesRegularExpression('/^[a-zA-Z]+$/', $second);
    }
}
