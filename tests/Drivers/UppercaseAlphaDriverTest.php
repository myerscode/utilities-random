<?php

declare(strict_types=1);

namespace Tests\Drivers;

use Myerscode\Utilities\Random\Drivers\UppercaseAlphaDriver;
use Tests\BaseTestSuite;

final class UppercaseAlphaDriverTest extends BaseTestSuite
{
    private UppercaseAlphaDriver $uppercaseAlphaDriver;

    protected function setUp(): void
    {
        $this->uppercaseAlphaDriver = new UppercaseAlphaDriver();
    }

    public function testDigestContainsOnlyUppercaseCharacters(): void
    {
        $seed = $this->uppercaseAlphaDriver->digest();
        $this->assertMatchesRegularExpression('/^[A-Z]+$/', $seed);
    }

    public function testDigestContainsExpectedLength(): void
    {
        $digest = $this->uppercaseAlphaDriver->digest();
        $this->assertSame(52, strlen($digest));
    }

    public function testSeedRegeneratesDigest(): void
    {
        $first = $this->uppercaseAlphaDriver->digest();
        $this->uppercaseAlphaDriver->seed();
        $second = $this->uppercaseAlphaDriver->digest();

        $this->assertSame(strlen($first), strlen($second));
        $this->assertMatchesRegularExpression('/^[A-Z]+$/', $second);
    }
}
