<?php

declare(strict_types=1);

namespace Myerscode\Utilities\Random\Drivers;

class NumericDriver extends AbstractDriver implements RandomDriverInterface
{
    public function seed(): void
    {
        $ranges = [
            implode('', array_map(strval(...), $this->shuffleArray(range(0, 9)))),
            implode('', array_map(strval(...), $this->shuffleArray(range(0, 9)))),
            implode('', array_map(strval(...), $this->shuffleArray(range(0, 9)))),
            implode('', array_map(strval(...), $this->shuffleArray(range(0, 9)))),
            implode('', array_map(strval(...), $this->shuffleArray(range(0, 9)))),
        ];

        $seed = $this->shuffleString(str_shuffle(implode('', $ranges)));

        for ($i = 0; $i < $this->iterations; $i++) {
            $seed = $this->shuffleString($seed);
        }

        $this->digest = $seed;
    }
}
