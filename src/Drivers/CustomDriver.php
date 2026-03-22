<?php

declare(strict_types=1);

namespace Myerscode\Utilities\Random\Drivers;

class CustomDriver extends AbstractDriver implements RandomDriverInterface
{
    /**
     * @param  array<int, string>  $characters  The character set to use
     */
    public function __construct(private readonly array $characters)
    {
        parent::__construct();
    }

    public function seed(): void
    {
        $ranges = [];

        for ($i = 0; $i < 5; $i++) {
            $ranges[] = implode('', array_map(strval(...), $this->shuffleArray($this->characters)));
        }

        $seed = $this->shuffleString(str_shuffle(implode('', $ranges)));

        for ($i = 0; $i < $this->iterations; $i++) {
            $seed = $this->shuffleString($seed);
        }

        $this->digest = $seed;
    }
}
