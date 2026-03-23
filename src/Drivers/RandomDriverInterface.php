<?php

declare(strict_types=1);

namespace Myerscode\Utilities\Random\Drivers;

interface RandomDriverInterface
{
    public function digest(): string;
    public function seed(): void;
}
