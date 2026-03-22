<?php

declare(strict_types=1);

namespace Myerscode\Utilities\Random\Rules;

/**
 * Rejects any generated string that contains consecutive repeating characters.
 */
class NoRepeatingCharacters implements ValidationRule
{
    public function passes(string $value): bool
    {
        $length = strlen($value);

        for ($i = 1; $i < $length; $i++) {
            if ($value[$i] === $value[$i - 1]) {
                return false;
            }
        }

        return true;
    }
}
