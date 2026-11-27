<?php

declare(strict_types=1);

namespace Myerscode\Utilities\Random\Drivers;

class BinaryDriver extends AbstractDriver implements RandomDriverInterface
{
    public function seed(): void
    {
        $this->digest = str_repeat('01', 25);
    }
}
