<?php

namespace Myerscode\Utilities\Random;

use Myerscode\Utilities\Random\Drivers\RandomDriverInterface;
use Myerscode\Utilities\Random\Exceptions\InvalidProviderException;
use Myerscode\Utilities\Random\Exceptions\UniqueThresholdReachedException;


class Utility
{

    /**
     * @var RandomDriverInterface
     */
    private $driver;

    /**
     * @var Generator
     */
    private $generator;

    /**
     * Generated codes in this instance
     *
     * @var array
     */
    private $generated = [];

    /**
     * How many times this generator created a random the same
     * @var int
     */
    private $collisions = 0;

    /**
     * How many times should unique try to generate a code before it gives up
     * This variable stops it getting stuck in a infinite loop when comparing against generated codes
     * @var int
     */
    private $uniqueCollisionThreshold = 10;

    private $chunks = 0;

    private $length = 5;

    private $spacer = '-';


    /**
     * @param $provider string|RandomDriverInterface
     *
     * @throws InvalidProviderException
     */
    public function __construct(RandomDriverInterface|string $provider)
    {
        if ($provider instanceof RandomDriverInterface || class_exists($provider) && ($provider = new $provider) instanceof RandomDriverInterface) {
            $this->driver = $provider;
        } else {
            throw new InvalidProviderException('You must provide a valid RandomDriver');
        }

        $this->generator = new Generator($this->driver);
    }

    /**
     * Seed the digest for creating random values
     */
    public function seed(): void
    {
        $this->driver->seed();
    }

    /**
     * Create the random value in chunks
     *
     * @param int $chunks
     * @return Utility
     */
    public function chunks(int $chunks): Utility
    {
        $this->chunks = $chunks;

        return $this;
    }

    /**
     * How long should the random value be
     *
     * @param int $length
     * @return Utility
     */
    public function length(int $length): Utility
    {
        $this->length = $length;

        return $this;
    }

    /**
     * Set a spacer value for multiple chunks
     *
     * @param string $spacer
     * @return Utility
     */
    public function spacer(string $spacer): Utility
    {
        $this->spacer = $spacer;

        return $this;
    }

    /**
     * Generate a random value
     *
     * @return string
     */
    public function generate(): string
    {
        $random = $this->make();

        $this->generated[] = $random;

        return $random;
    }

    /**
     * Generate a value not created with this instance
     *
     * @return string
     * @throws UniqueThresholdReachedException
     */
    public function unique(): string
    {
        $unique = false;

        $iterations = 0;

        $random = '';

        while ($unique === false && $iterations < $this->uniqueCollisionThreshold) {

            $iterations++;

            $random = $this->make();

            if (!in_array($random, $this->generated)) {
                $unique = true;
            } else {
                $this->collisions++;
            }
        }

        if ($iterations >= $this->uniqueCollisionThreshold) {
            throw new UniqueThresholdReachedException(sprintf("Maximum attempts (%s) at creating a new unique value reached", $this->uniqueCollisionThreshold));
        } else {
            $this->generated[] = $random;
            return $random;
        }

    }

    private function make()
    {
        return $this->generator->make($this->length, $this->chunks, $this->spacer);
    }

    /**
     * How many collisions occurred creating values in this instance
     *
     * @return int
     */
    public function collisions(): int
    {
        return $this->collisions;
    }

    /**
     * Reset the utility instance, keeping the current seeder digest
     *
     * @return Utility
     */
    public function reset(): Utility
    {
        $this->collisions = 0;
        $this->generated = [];

        return $this;
    }

    public function permutations()
    {
        return pow(strlen($this->driver->digest()), $this->length);
    }
}
