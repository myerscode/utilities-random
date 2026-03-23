<?php

declare(strict_types=1);

namespace Tests\Drivers;

use Myerscode\Utilities\Random\Drivers\AlphaNumericDriver;
use Tests\BaseTestSuite;

final class AlphaNumericDriverTest extends BaseTestSuite
{
    private AlphaNumericDriver $alphaNumericDriver;

    protected function setUp(): void
    {
        $this->alphaNumericDriver = new AlphaNumericDriver();
    }

    public function testDigestContainsExpectedLength(): void
    {
        $digest = $this->alphaNumericDriver->digest();
        $this->assertSame(62, strlen($digest));
    }

    public function testDigestContainsOnlyAlphaNumericCharacters(): void
    {
        $seed = $this->alphaNumericDriver->digest();
        $this->assertMatchesRegularExpression('/^[a-zA-Z0-9]+$/', $seed);
    }

    public function testSeedRegeneratesDigest(): void
    {
        $first = $this->alphaNumericDriver->digest();
        $this->alphaNumericDriver->seed();
        $second = $this->alphaNumericDriver->digest();

        $this->assertSame(strlen($first), strlen($second));
        $this->assertMatchesRegularExpression('/^[a-zA-Z0-9]+$/', $second);
    }
}
