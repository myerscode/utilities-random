<?php

declare(strict_types=1);

namespace Myerscode\Utilities\Random\Rules;

/**
 * Requires the generated string to contain at least X uppercase letters.
 */
class MustContainUppercase extends MustContainPattern
{
    protected function pattern(): string
    {
        return '/[A-Z]/';
    }
}
