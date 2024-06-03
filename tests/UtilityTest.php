<?php

namespace Tests;

use Myerscode\Utilities\Random\Drivers\AlphaNumericDriver;
use Myerscode\Utilities\Random\Utility;
use Tests\Support\BaseTestSuite;

/**
 * @coversDefaultClass \Myerscode\Utilities\Random\Utility
 */
class UtilityTest extends BaseTestSuite
{

    public function dataProvider(): array
    {
        return [
            [AlphaNumericDriver::class],
        ];
    }

    /**
     * Test a value is appended to the string
     *
     * @param string $driver
     * @dataProvider dataProvider
     * @covers ::__construct
     */
    public function testConstructor($driver)
    {
        $this->assertInstanceOf(Utility::class, $this->utility($driver));
    }
}
