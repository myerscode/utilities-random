<?php

namespace Myerscode\Utilities\Random\Drivers;


class AlphaNumericDriver extends AbstractDriver implements RandomDriverInterface
{

    public function __construct()
    {
        $this->seed();
    }

    /**
     * Seed the driver
     */
    public function seed(): void
    {
        $ranges = [
            implode('', $this->shuffle(range('a', 'z'))),
            implode('', $this->shuffle(range('A', 'Z'))),
            implode('', $this->shuffle(range(0, 9))),
        ];

        $this->digest = str_shuffle(implode('', $ranges));

        for ($i = -0; $i < $this->iterations; $i++) {
            $this->digest = str_shuffle($this->digest);
        }
    }

    private function shuffle($array): array
    {
        $result = [];

        $keys = array_keys($array);

        for ($count = count($array); $count > 0; $count--) {

            $index = random_int(0, $count - 1);

            $result[$keys[$index]] = $array[$keys[$index]];

            if ($index < $count - 1) {
                $keys[$index] = $keys[$count - 1];
            }
        }

        return $result;
    }
}