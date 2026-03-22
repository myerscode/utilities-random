<?php

declare(strict_types=1);

namespace Myerscode\Utilities\Random\Rules;

/**
 * Removes visually similar characters from the pool (e.g. oO0, I1l).
 */
class ExcludeSimilarCharacters implements PoolRule
{
    /** @var array<int, string> */
    private array $similar = ['o', 'O', '0', 'I', '1', 'l'];

    public function filter(string $pool): string
    {
        return implode('', array_filter(
            str_split($pool),
            fn (string $char): bool => !in_array($char, $this->similar, true),
        ));
    }
}
