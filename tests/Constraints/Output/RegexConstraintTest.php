<?php

declare(strict_types=1);

namespace Tests\Constraints\Output;

use Myerscode\Utilities\Random\Constraints\Output\RegexConstraint;
use Myerscode\Utilities\Random\Exceptions\InvalidConstraintException;
use Tests\BaseTestSuite;

final class RegexConstraintTest extends BaseTestSuite
{
    public function testFailsWhenPatternDoesNotMatch(): void
    {
        $regexConstraint = new RegexConstraint('/^[a-z]+$/');
        $this->assertFalse($regexConstraint->passes('ABC123'));
    }

    public function testPassesWhenPatternMatches(): void
    {
        $regexConstraint = new RegexConstraint('/^[a-z]+$/');
        $this->assertTrue($regexConstraint->passes('abcdef'));
    }

    public function testPassesWithEmptyStringWhenPatternAllows(): void
    {
        $regexConstraint = new RegexConstraint('/^.*$/');
        $this->assertTrue($regexConstraint->passes(''));
    }
    public function testThrowsWhenPatternIsInvalid(): void
    {
        $this->expectException(InvalidConstraintException::class);
        $this->expectExceptionMessage('Invalid regex pattern [/(unclosed/]');

        new RegexConstraint('/(unclosed/');
    }

    public function testWorksWithDigitPattern(): void
    {
        $regexConstraint = new RegexConstraint('/\d/');
        $this->assertTrue($regexConstraint->passes('abc1'));
        $this->assertFalse($regexConstraint->passes('abcd'));
    }

    public function testWorksWithStartAndEndAnchors(): void
    {
        $regexConstraint = new RegexConstraint('/^[A-Z]{3}\d{3}$/');
        $this->assertTrue($regexConstraint->passes('ABC123'));
        $this->assertFalse($regexConstraint->passes('abc123'));
        $this->assertFalse($regexConstraint->passes('ABCD123'));
    }
}
