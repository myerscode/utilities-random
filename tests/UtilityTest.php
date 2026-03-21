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
     *
     * @throws InvalidProviderException
     *
     * @dataProvider dataProvider
     */
    public function testConstructor(string $driver): void
    {
        $this->assertInstanceOf(Utility::class, $this->utility($driver));
    }
}
