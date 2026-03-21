<?php

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

    public function testSeedGeneration(): void
    {
        $seed = $this->driver->digest();
        $this->assertMatchesRegularExpression('/^\d*$/', $seed);
    }
}
