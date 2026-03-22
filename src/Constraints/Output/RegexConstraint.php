<?php

declare(strict_types=1);

namespace Myerscode\Utilities\Random\Constraints\Output;

use Myerscode\Utilities\Random\Constraints\OutputConstraint;

/**
 * Validates the generated string against a user-provided regex pattern.
 */
class RegexConstraint implements OutputConstraint
{
    public function __construct(private readonly string $pattern) {}

    public function passes(string $value): bool
    {
        return (bool) preg_match($this->pattern, $value);
    }

    /**
     * Regex patterns are too varied to predict satisfiability from the pool,
     * so this always returns true — validation happens at generation time.
     */
    public function canBeSatisfiedBy(string $pool, int $length): bool
    {
        return true;
    }
}
