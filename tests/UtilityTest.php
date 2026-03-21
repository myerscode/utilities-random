<?php

namespace Tests;

use Myerscode\Utilities\Random\Drivers\AlphaNumericDriver;
use Myerscode\Utilities\Random\Exceptions\InvalidProviderException;
use Myerscode\Utilities\Random\Utility;
use PHPUnit\Framework\Attributes\DataProvider;

class UtilityTest extends BaseTestSuite
{
    public static function dataProvider(): array
    {
        return [
            [AlphaNumericDriver::class],
        ];
    }

    /**
     * @throws InvalidProviderException
     */
    #[DataProvider('dataProvider')]
    public function testConstructor(string $driver): void
    {
        $this->assertInstanceOf(Utility::class, $this->utility($driver));
    }
}
