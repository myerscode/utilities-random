<?php

declare(strict_types=1);

namespace Myerscode\Utilities\Random\Drivers;

class AlphaNumericDriver extends AbstractDriver implements RandomDriverInterface
{
    public function seed(): void
    {
        $ranges = [
            implode('', array_map(strval(...), $this->shuffleArray(range('a', 'z')))),
            implode('', array_map(strval(...), $this->shuffleArray(range('A', 'Z')))),
            implode('', array_map(strval(...), $this->shuffleArray(range(0, 9)))),
        ];

        $ranges = $this->shuffleArray($ranges);

        $seed = $this->shuffleString(str_shuffle(implode('', array_map(strval(...), $ranges))));

        for ($i = 0; $i < $this->iterations; $i++) {
            $seed = $this->shuffleString($seed);
        }

        $this->digest = $seed;
    }
}
