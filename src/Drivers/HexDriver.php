<?php

declare(strict_types=1);

namespace Myerscode\Utilities\Random\Drivers;

class HexDriver extends AbstractDriver implements RandomDriverInterface
{
    public function seed(): void
    {
        $this->digest = implode('', range(0, 9)) . implode('', range('a', 'f'));
    }
}
