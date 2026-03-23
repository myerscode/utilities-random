<?php

declare(strict_types=1);

namespace Tests\Drivers;

use Myerscode\Utilities\Random\Drivers\BinaryDriver;
use Tests\BaseTestSuite;

final class BinaryDriverTest extends BaseTestSuite
{
    private BinaryDriver $binaryDriver;

    protected function setUp(): void
    {
        $this->binaryDriver = new BinaryDriver();
    }

    public function testDigestContainsExpectedLength(): void
    {
        $digest = $this->binaryDriver->digest();
        $this->assertSame(50, strlen($digest));
    }

    public function testDigestContainsOnlyBinaryCharacters(): void
    {
        $seed = $this->binaryDriver->digest();
        $this->assertMatchesRegularExpression('/^[01]+$/', $seed);
    }

    public function testSeedRegeneratesDigest(): void
    {
        $first = $this->binaryDriver->digest();
        $this->binaryDriver->seed();
        $second = $this->binaryDriver->digest();

        $this->assertSame(strlen($first), strlen($second));
        $this->assertMatchesRegularExpression('/^[01]+$/', $second);
    }
}
