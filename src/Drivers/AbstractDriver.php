<?php
/**
 * Created by PhpStorm.
 * User: Fred
 * Date: 21/03/2018
 * Time: 21:15
 */

namespace Myerscode\Utilities\Random\Drivers;


use Random\RandomException;

abstract class AbstractDriver
{

    protected int $iterations = 5000;

    protected string $digest;


    public function __construct()
    {
        $this->seed();
    }

    /**
     * Seed the digest used for creating the random result
     */
    abstract public function seed(): void;

    public function digest(): string
    {
        return $this->digest;
    }

    /**
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

        return str_split(strrev(str_shuffle(implode('', $result))));
    }

    protected function shuffleString(string $string): string
    {
        return strrev(str_shuffle($string));
    }
}
