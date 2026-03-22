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

    /**
     * For length 1, any pool works since there are no adjacent characters.
     * For length 2+, the pool needs at least 2 distinct characters, otherwise
     * every generated string will be "AAAA..." which always has repeats.
     */
    public function canBeSatisfiedBy(string $pool, int $length): bool
    {
        if ($length <= 1) {
            return true;
        }

        return count(array_unique(str_split($pool))) >= 2;
    }
}
