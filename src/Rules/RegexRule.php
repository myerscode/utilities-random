<?php

declare(strict_types=1);

namespace Myerscode\Utilities\Random\Rules;

/**
 * Validates the generated string against a user-provided regex pattern.
 */
class RegexRule implements ValidationRule
{
    public function __construct(private readonly string $pattern) {}

    public function passes(string $value): bool
    {
        return (bool) preg_match($this->pattern, $value);
    }
}
