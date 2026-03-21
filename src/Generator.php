<?php

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
        if ($chunkLength < 1) {
            $chunkLength = 1;
        }

        // if 0 chunks you are just creating 1 string, so its really 1 chunk
        if ($numChunks <= 1) {
            $numChunks = 1;
            $spacer = '';
        }

        $new_serial_chunks = [];
        
        for ($x = 0; $x < $numChunks; $x++) {
            $new_serial_chunk = '';

            for ($y = 0; $y < $chunkLength; $y++) {
                $new_serial_chunk .= $this->pool[random_int(0, $this->poolLength - 1)];
            }

            $new_serial_chunks[] = $new_serial_chunk;
        }

        return implode($spacer, $new_serial_chunks);
    }
}
