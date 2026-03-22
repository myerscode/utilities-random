<?php

declare(strict_types=1);

namespace Myerscode\Utilities\Random\Constraints\Output;

use Myerscode\Utilities\Random\Constraints\MustContainPattern;

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
