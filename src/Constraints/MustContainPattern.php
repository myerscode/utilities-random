<?php

declare(strict_types=1);

namespace Myerscode\Utilities\Random\Constraints;

use Myerscode\Utilities\Random\Exceptions\InvalidConstraintException;

/**
 * Base class for constraints that require a minimum number of characters matching a regex pattern.
 */
abstract class MustContainPattern implements OutputConstraint
{
    /**
     * @throws InvalidConstraintException
     */
    public function __construct(private readonly int $minimum = 1)
    {
        if (@preg_match($this->pattern(), '') === false) {
            throw new InvalidConstraintException(sprintf('Invalid regex pattern [%s]', $this->pattern()));
        }
    }

    /**
     * The regex pattern that characters must match.
     * Concrete constraints define this to specify what they're looking for
     * (e.g. '/\d/' for digits, '/[A-Z]/' for uppercase).
     */
    abstract protected function pattern(): string;

    /**
     * Check the pool contains at least one character matching the pattern and the
     * minimum fits in the requested length. Otherwise this constraint can never pass
     * regardless of how many times we retry generation.
     */
    public function canBeSatisfiedBy(string $pool, int $length): bool
    {
        return $this->minimum <= $length && preg_match($this->pattern(), $pool) === 1;
    }

    /**
     * Check the generated string contains at least the minimum number
     * of characters matching the pattern.
     */
    public function passes(string $value): bool
    {
        return preg_match_all($this->pattern(), $value) >= $this->minimum;
    }
}
