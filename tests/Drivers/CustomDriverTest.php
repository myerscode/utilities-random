<?php

declare(strict_types=1);

namespace Tests\Drivers;

use Myerscode\Utilities\Random\Drivers\CustomDriver;
use Myerscode\Utilities\Random\Utility;
use Tests\BaseTestSuite;

class CustomDriverTest extends BaseTestSuite
{
    public function testDigestContainsOnlyProvidedCharacters(): void
    {
        $driver = new CustomDriver(['x', 'y', 'z']);
        $seed = $driver->digest();
        $this->assertMatchesRegularExpression('/^[xyz]+$/', $seed);
    }

    public function testDigestLengthScalesWithCharacterCount(): void
    {
        $driver = new CustomDriver(['a', 'b', 'c']);
        $this->assertSame(15, strlen($driver->digest()));
    }

    public function testSeedRegeneratesDigest(): void
    {
        $driver = new CustomDriver(['a', 'b']);
        $first = $driver->digest();
        $driver->seed();
        $second = $driver->digest();

        $this->assertSame(strlen($first), strlen($second));
        $this->assertMatchesRegularExpression('/^[ab]+$/', $second);
    }

    public function testWorksWithUtility(): void
    {
        $driver = new CustomDriver(['1', '2', '3']);
        $utility = new Utility($driver);
        $result = $utility->length(10)->generate();

        $this->assertSame(10, strlen($result));
        $this->assertMatchesRegularExpression('/^[123]+$/', $result);
    }

    public function testWorksWithSingleCharacter(): void
    {
        $driver = new CustomDriver(['A']);
        $this->assertMatchesRegularExpression('/^A+$/', $driver->digest());
    }

    public function testWorksWithSpecialCharacters(): void
    {
        $driver = new CustomDriver(['!', '@', '#', '$']);
        $seed = $driver->digest();
        $this->assertMatchesRegularExpression('/^[!@#$]+$/', $seed);
    }
}
