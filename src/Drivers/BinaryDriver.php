<?php

declare(strict_types=1);

namespace Myerscode\Utilities\Random\Drivers;

class BinaryDriver extends AbstractDriver implements RandomDriverInterface
{
    public function seed(): void
    {
        $ranges = [];

        for ($i = 0; $i < 25; $i++) {
            $ranges[] = implode('', array_map(strval(...), $this->shuffleArray(range(0, 1))));
        }

        $seed = $this->shuffleString(str_shuffle(implode('', $ranges)));

        for ($i = 0; $i < $this->iterations; $i++) {
            $seed = $this->shuffleString($seed);
        }

        $this->digest = $seed;
    }
}
