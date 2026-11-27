<?php

declare(strict_types=1);

namespace Myerscode\Utilities\Random\Drivers;

class NumericDriver extends AbstractDriver implements RandomDriverInterface
{
    public function seed(): void
    {
        $this->digest = str_repeat(implode('', range(0, 9)), 5);
    }
}
