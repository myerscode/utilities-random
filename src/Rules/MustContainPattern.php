<?php

declare(strict_types=1);

namespace Myerscode\Utilities\Random\Rules;

/**
 * Base class for rules that require a minimum number of characters matching a regex pattern.
 */
abstract class MustContainPattern implements ValidationRule
{
    public function __construct(private readonly int $minimum = 1) {}

    /**
     * The regex pattern that characters must match.
     * Concrete rules define this to specify what they're looking for
     * (e.g. '/\d/' for digits, '/[A-Z]/' for uppercase).
     */
    abstract protected function pattern(): string;

    /**
     * Check the generated string contains at least the minimum number
     * of characters matching the pattern.
     */
    public function passes(string $value): bool
    {
        return preg_match_all($this->pattern(), $value) >= $this->minimum;
    }

    /**
     * Check the pool contains at least one character matching the pattern.
     * If the pool has no matching characters, this rule can never pass
     * regardless of how many times we retry generation.
     */
    public function canBeSatisfiedBy(string $pool, int $length): bool
    {
        return (bool) preg_match($this->pattern(), $pool);
    }
}
