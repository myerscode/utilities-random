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
}
