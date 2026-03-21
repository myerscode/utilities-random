<?php

declare(strict_types=1);

namespace Myerscode\Utilities\Random;

use Myerscode\Utilities\Random\Drivers\RandomDriverInterface;

class Generator
{
    private string $pool;

    private int $poolLength;

    public function __construct(protected readonly RandomDriverInterface $driver)
    {
        $this->setPool($this->driver->digest());
    }

    public function setPool(string $pool): void
    {
        $this->pool = $pool;
        $this->poolLength = strlen($this->pool);
    }

    public function getPool(): string
    {
        return $this->pool;
    }

    public function make(int $chunkLength = 4, int $numChunks = 1, string $spacer = ''): string
    {
        $chunkLength = max(1, $chunkLength);

        if ($numChunks <= 1) {
            $numChunks = 1;
            $spacer = '';
        }

        $chunks = [];

        for ($x = 0; $x < $numChunks; $x++) {
            $chunk = '';

            for ($y = 0; $y < $chunkLength; $y++) {
                $chunk .= $this->pool[random_int(0, $this->poolLength - 1)];
            }

            $chunks[] = $chunk;
        }

        return implode($spacer, $chunks);
    }
}
