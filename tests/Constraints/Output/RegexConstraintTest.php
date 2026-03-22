<?php

declare(strict_types=1);

namespace Tests\Constraints\Output;

use Myerscode\Utilities\Random\Constraints\Output\RegexConstraint;
use Tests\BaseTestSuite;

class RegexConstraintTest extends BaseTestSuite
{
    public function testPassesWhenPatternMatches(): void
    {
        $rule = new RegexConstraint('/^[a-z]+$/');
        $this->assertTrue($rule->passes('abcdef'));
    }

    public function testFailsWhenPatternDoesNotMatch(): void
    {
        $rule = new RegexConstraint('/^[a-z]+$/');
        $this->assertFalse($rule->passes('ABC123'));
    }

    public function testWorksWithDigitPattern(): void
    {
        $rule = new RegexConstraint('/\d/');
        $this->assertTrue($rule->passes('abc1'));
        $this->assertFalse($rule->passes('abcd'));
    }

    public function testWorksWithStartAndEndAnchors(): void
    {
        $rule = new RegexConstraint('/^[A-Z]{3}\d{3}$/');
        $this->assertTrue($rule->passes('ABC123'));
        $this->assertFalse($rule->passes('abc123'));
        $this->assertFalse($rule->passes('ABCD123'));
    }

    public function testPassesWithEmptyStringWhenPatternAllows(): void
    {
        $rule = new RegexConstraint('/^.*$/');
        $this->assertTrue($rule->passes(''));
    }
}
