<?php

declare(strict_types=1);

namespace Myerscode\Utilities\Random\Drivers;

interface RandomDriverInterface
{
    public function seed(): void;

    public function digest(): string;
}
