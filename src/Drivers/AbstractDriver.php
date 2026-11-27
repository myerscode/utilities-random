<?php

declare(strict_types=1);

namespace Myerscode\Utilities\Random\Drivers;

abstract class AbstractDriver
{
    protected string $digest;

    public function __construct()
    {
        $this->seed();
    }

    abstract public function seed(): void;

    public function digest(): string
    {
        return $this->digest;
    }
}
