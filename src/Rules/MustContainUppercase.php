<?php

declare(strict_types=1);

namespace Myerscode\Utilities\Random\Rules;

/**
 * Requires the generated string to contain at least X uppercase letters.
 */
class MustContainUppercase implements ValidationRule
{
    public function __construct(private readonly int $minimum = 1) {}

    public function passes(string $value): bool
    {
        return preg_match_all('/[A-Z]/', $value) >= $this->minimum;
    }
}
