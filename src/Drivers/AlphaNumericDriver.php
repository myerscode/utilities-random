<?php

declare(strict_types=1);

namespace Myerscode\Utilities\Random\Drivers;

class AlphaNumericDriver extends AbstractDriver implements RandomDriverInterface
{
    public function seed(): void
    {
        $this->digest = implode('', range('a', 'z')) . implode('', range('A', 'Z')) . implode('', range(0, 9));
    }
}
