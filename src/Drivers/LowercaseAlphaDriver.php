<?php

declare(strict_types=1);

namespace Myerscode\Utilities\Random\Drivers;

class LowercaseAlphaDriver extends AbstractDriver implements RandomDriverInterface
{
    public function seed(): void
    {
        $this->digest = str_repeat(implode('', range('a', 'z')), 2);
    }
}
