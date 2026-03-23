<?php

declare(strict_types=1);

namespace Myerscode\Utilities\Random\Constraints\Pool;

use Myerscode\Utilities\Random\Constraints\PoolConstraint;

/**
 * Removes a configurable set of characters from the pool.
 */
class ExcludeCharacters implements PoolConstraint
{
    /**
     * @param array<int, string> $excluded Characters to exclude from the pool
     */
    public function __construct(private readonly array $excluded)
    {
    }

    public function filter(string $pool): string
    {
        return implode('', array_filter(
            str_split($pool),
            fn (string $char): bool => !in_array($char, $this->excluded, true),
        ));
    }
}
