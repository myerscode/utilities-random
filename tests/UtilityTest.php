<?php

namespace Tests;

use Myerscode\Utilities\Random\Drivers\AlphaNumericDriver;
use Myerscode\Utilities\Random\Exceptions\InvalidProviderException;
use Myerscode\Utilities\Random\Utility;

class UtilityTest extends BaseTestSuite
{

    public static function dataProvider(): array
    {
        return [
            [AlphaNumericDriver::class],
        ];
    }

    /**
     * @param  string  $driver
     *
     * @return void
     * @throws InvalidProviderException
     *
     * @dataProvider dataProvider
     */
    public function testConstructor(string $driver)
    {
        $this->assertInstanceOf(Utility::class, $this->utility($driver));
    }
}
