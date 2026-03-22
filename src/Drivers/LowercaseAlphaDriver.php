<?php

declare(strict_types=1);

namespace Myerscode\Utilities\Random\Drivers;

class LowercaseAlphaDriver extends AbstractDriver implements RandomDriverInterface
{
    public function seed(): void
    {
        $ranges = [
            implode('', array_map(strval(...), $this->shuffleArray(range('a', 'z')))),
            implode('', array_map(strval(...), $this->shuffleArray(range('a', 'z')))),
        ];

        $seed = $this->shuffleString(str_shuffle(implode('', $ranges)));

        for ($i = 0; $i < $this->iterations; $i++) {
            $seed = $this->shuffleString($seed);
        }

        $this->digest = $seed;
    }
}
