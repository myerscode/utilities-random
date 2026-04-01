<?php

declare(strict_types=1);

namespace Myerscode\Utilities\Random\Drivers;

use Random\RandomException;

abstract class AbstractDriver
{
    protected string $digest;

    protected int $iterations = 5000;

    public function __construct()
    {
        $this->seed();
    }

    abstract public function seed(): void;

    public function digest(): string
    {
        return $this->digest;
    }

    /**
     * @param  array<int|string, int|string>  $array
     * @return array<int, string>
     *
     * @throws RandomException
     */
    protected function shuffleArray(array $array): array
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

        return str_split(strrev(str_shuffle(implode('', array_map(strval(...), $result)))));
    }

    protected function shuffleString(string $string): string
    {
        return strrev(str_shuffle($string));
    }
}
