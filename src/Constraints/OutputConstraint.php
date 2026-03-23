<?php

declare(strict_types=1);

namespace Myerscode\Utilities\Random\Constraints;

/**
 * Output constraints check a generated string and reject it if it doesn't meet criteria.
 */
interface OutputConstraint extends ConstraintInterface
{
    /**
     * Return true if this constraint can potentially be satisfied given the available pool
     * and the requested output length. Used for early conflict detection before
     * generation begins.
     */
    public function canBeSatisfiedBy(string $pool, int $length): bool;
    /**
     * Return true if the generated string passes validation.
     */
    public function passes(string $value): bool;
}
