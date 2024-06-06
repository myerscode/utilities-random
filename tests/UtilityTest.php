<?php

namespace Tests;

use Myerscode\Utilities\Random\Drivers\AlphaNumericDriver;
use Myerscode\Utilities\Random\Utility;
use Tests\Support\BaseTestSuite;

class UtilityTest extends BaseTestSuite
{

    public static function dataProvider(): array
    {
        return [
            [AlphaNumericDriver::class],
        ];
    }

    /**
     * Test a value is appended to the string
     *
     * @param  string  $driver
     *
     * @dataProvider dataProvider
     */
    public function testConstructor(string $driver)
    {
        $this->assertInstanceOf(Utility::class, $this->utility($driver));
    }
}
