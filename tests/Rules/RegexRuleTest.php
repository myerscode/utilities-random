<?php

declare(strict_types=1);

namespace Tests\Rules;

use Myerscode\Utilities\Random\Rules\RegexRule;
use Tests\BaseTestSuite;

class RegexRuleTest extends BaseTestSuite
{
    public function testPassesWhenPatternMatches(): void
    {
        $rule = new RegexRule('/^[a-z]+$/');
        $this->assertTrue($rule->passes('abcdef'));
    }

    public function testFailsWhenPatternDoesNotMatch(): void
    {
        $rule = new RegexRule('/^[a-z]+$/');
        $this->assertFalse($rule->passes('ABC123'));
    }

    public function testWorksWithDigitPattern(): void
    {
        $rule = new RegexRule('/\d/');
        $this->assertTrue($rule->passes('abc1'));
        $this->assertFalse($rule->passes('abcd'));
    }

    public function testWorksWithStartAndEndAnchors(): void
    {
        $rule = new RegexRule('/^[A-Z]{3}\d{3}$/');
        $this->assertTrue($rule->passes('ABC123'));
        $this->assertFalse($rule->passes('abc123'));
        $this->assertFalse($rule->passes('ABCD123'));
    }

    public function testPassesWithEmptyStringWhenPatternAllows(): void
    {
        $rule = new RegexRule('/^.*$/');
        $this->assertTrue($rule->passes(''));
    }
}
