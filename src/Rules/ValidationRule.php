<?php

declare(strict_types=1);

namespace Myerscode\Utilities\Random\Rules;

/**
 * Validation rules check a generated string and reject it if it doesn't meet criteria.
 */
interface ValidationRule extends RuleInterface
{
    /**
     * Return true if the generated string passes validation.
     */
    public function passes(string $value): bool;

    /**
     * Return true if this rule can potentially be satisfied given the available pool
     * and the requested output length. Used for early conflict detection before
     * generation begins.
     */
    public function canBeSatisfiedBy(string $pool, int $length): bool;
}
