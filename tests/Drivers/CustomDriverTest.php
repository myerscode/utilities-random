<?php

declare(strict_types=1);

namespace Tests\Drivers;

use Myerscode\Utilities\Random\Drivers\CustomDriver;
use Myerscode\Utilities\Random\Utility;
use Tests\BaseTestSuite;

final class CustomDriverTest extends BaseTestSuite
{
    public function testDigestContainsOnlyProvidedCharacters(): void
    {
        $customDriver = new CustomDriver(['x', 'y', 'z']);
        $seed = $customDriver->digest();
        $this->assertMatchesRegularExpression('/^[xyz]+$/', $seed);
    }

    public function testDigestLengthScalesWithCharacterCount(): void
    {
        $customDriver = new CustomDriver(['a', 'b', 'c']);
        $this->assertSame(15, strlen($customDriver->digest()));
    }

    public function testSeedRegeneratesDigest(): void
    {
        $customDriver = new CustomDriver(['a', 'b']);
        $first = $customDriver->digest();
        $customDriver->seed();
        $second = $customDriver->digest();

        $this->assertSame(strlen($first), strlen($second));
        $this->assertMatchesRegularExpression('/^[ab]+$/', $second);
    }

    public function testWorksWithSingleCharacter(): void
    {
        $customDriver = new CustomDriver(['A']);
        $this->assertMatchesRegularExpression('/^A+$/', $customDriver->digest());
    }

    public function testWorksWithSpecialCharacters(): void
    {
        $customDriver = new CustomDriver(['!', '@', '#', '$']);
        $seed = $customDriver->digest();
        $this->assertMatchesRegularExpression('/^[!@#$]+$/', $seed);
    }

    public function testWorksWithUtility(): void
    {
        $customDriver = new CustomDriver(['1', '2', '3']);
        $utility = new Utility($customDriver);
        $result = $utility->length(10)->generate();

        $this->assertSame(10, strlen($result));
        $this->assertMatchesRegularExpression('/^[123]+$/', $result);
    }
}
