<?php

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

    public function testSeedGeneration(): void
    {
        $seed = $this->driver->digest();
        $this->assertMatchesRegularExpression('/^[a-zA-Z0-9]*$/', $seed);
    }
}
