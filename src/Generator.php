<?php

namespace Myerscode\Utilities\Random;
use Myerscode\Utilities\Random\Drivers\RandomDriverInterface;

/**
 * Class Utility
 *
 * @package Myerscode\Utilities\Random
 */
class Generator
{
    private RandomDriverInterface $driver;

    /**
     * @var string
     */
    private $pool;

    /**
     * @var int
     */
    private $poolLength;

    public function __construct(RandomDriverInterface $driver)
    {
        $this->driver = $driver;

        $this->setPool($this->driver->digest());
    }

    public function setPool(string $pool)
    {
        $this->pool = $pool;
        $this->poolLength = strlen($this->pool);
    }

    /**
     * @param  int  $chunkLength
     * @param  int  $numChunks
     * @param  string  $spacer
     *
     * @return string
     */
    public function make(int $chunkLength = 4, int $numChunks = 1, string $spacer = '')
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

        $new_serial = null;

        for ($x = 0; $x < $numChunks; $x++) {
            $new_serial_chunk = '';

            for ($y = 0; $y < $chunkLength; $y++) {
                $new_serial_chunk .= (string)$this->pool[rand(0, $this->poolLength - 1)];
            }

            $new_serial_chunks[] = $new_serial_chunk;
        }

        return implode($spacer, $new_serial_chunks);
    }
}
