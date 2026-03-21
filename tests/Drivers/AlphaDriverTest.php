<?php

namespace Tests\Drivers;

use Myerscode\Utilities\Random\Drivers\AlphaDriver;
use Tests\BaseTestSuite;

class AlphaDriverTest extends BaseTestSuite
{
    protected AlphaDriver $driver;

    protected function setUp(): void
    {
        $this->driver = new AlphaDriver();
    }

    public function testSeedGeneration(): void
    {
        $seed = $this->driver->digest();
        $this->assertMatchesRegularExpression('/^[a-zA-Z]*$/', $seed);
    }
}
