<?php

declare(strict_types=1);

namespace Myerscode\Utilities\Random\Rules;

/**
 * Requires the generated string to contain at least X letters.
 */
class MustContainLetter extends MustContainPattern
{
    protected function pattern(): string
    {
        return '/[a-zA-Z]/';
    }
}
