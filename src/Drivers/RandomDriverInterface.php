<?php

namespace Myerscode\Utilities\Random\Drivers;


interface RandomDriverInterface
{

    /**
     * Seed the generator
     */
    public function seed(): void;

    /**
     * Get the random digest pool
     *
     * @return string
     */
    public function digest(): string;
}