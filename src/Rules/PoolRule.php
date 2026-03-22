<?php

declare(strict_types=1);

namespace Myerscode\Utilities\Random\Rules;

/**
 * Pool rules filter the character pool before generation.
 * They remove characters that should never appear in the output.
 */
interface PoolRule extends RuleInterface
{
    /**
     * Filter the character pool, returning only allowed characters.
     */
    public function filter(string $pool): string;
}
