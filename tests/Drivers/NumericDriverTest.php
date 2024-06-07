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

    public function testSeedGeneration()
    {
        $seed = $this->driver->digest();
        $this->assertMatchesRegularExpression('/^[0-9]*$/', $seed);
    }
}
