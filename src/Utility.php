<?php

namespace Myerscode\Utilities\Random;

use Myerscode\Utilities\Random\Drivers\RandomDriverInterface;
use Myerscode\Utilities\Random\Exceptions\InvalidProviderException;
use Myerscode\Utilities\Random\Exceptions\UniqueThresholdReachedException;


class Utility
{

    private RandomDriverInterface $driver;

    /**
     * @var Generator
     */
    private readonly Generator $generator;

    /**
     * Generated codes in this instance
     */
    private array $generated = [];

    /**
     * How many times this generator created a random the same
     */
    private int $collisions = 0;

    /**
     * How many times should unique try to generate a code before it gives up
     * This variable stops it's getting stuck in an infinite loop when comparing against generated codes
     */
    private int $uniqueCollisionThreshold = 10;

    private int $chunks = 0;

    private int $length = 5;

    private string $spacer = '-';


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
     */
    public function chunks(int $chunks): Utility
    {
        $this->chunks = $chunks;

        return $this;
    }

    /**
     * How long should the random value be
     */
    public function length(int $length): Utility
    {
        $this->length = $length;

        return $this;
    }

    /**
     * Set a spacer value for multiple chunks
     */
    public function spacer(string $spacer): Utility
    {
        $this->spacer = $spacer;

        return $this;
    }

    /**
     * Generate a random value
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
        }
        $this->generated[] = $random;
        return $random;

    }

    private function make(): string
    {
        return $this->generator->make($this->length, $this->chunks, $this->spacer);
    }

    /**
     * How many collisions occurred creating values in this instance
     */
    public function collisions(): int
    {
        return $this->collisions;
    }

    /**
     * Reset the utility instance, keeping the current seeder digest
     */
    public function reset(): Utility
    {
        $this->collisions = 0;
        $this->generated = [];

        return $this;
    }

    public function permutations(): int
    {
        return strlen($this->driver->digest()) ** $this->length;
    }
}
