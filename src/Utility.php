<?php

declare(strict_types=1);

namespace Myerscode\Utilities\Random;

use Myerscode\Utilities\Random\Drivers\RandomDriverInterface;
use Myerscode\Utilities\Random\Exceptions\InvalidProviderException;
use Myerscode\Utilities\Random\Exceptions\UniqueThresholdReachedException;

class Utility
{
    private RandomDriverInterface $driver;

    private readonly Generator $generator;

    private array $generated = [];

    private int $collisions = 0;

    private int $uniqueCollisionThreshold = 10;

    private int $chunks = 0;

    private int $length = 5;

    private string $spacer = '-';

    /**
     * @throws InvalidProviderException
     */
    public function __construct(RandomDriverInterface|string $provider)
    {
        if (is_string($provider)) {
            if (!class_exists($provider)) {
                throw new InvalidProviderException('You must provide a valid RandomDriver');
            }

            $provider = new $provider();

            if (!$provider instanceof RandomDriverInterface) {
                throw new InvalidProviderException('You must provide a valid RandomDriver');
            }
        }

        $this->driver = $provider;
        $this->generator = new Generator($this->driver);
    }

    public function seed(): void
    {
        $this->driver->seed();
    }

    public function chunks(int $chunks): static
    {
        $this->chunks = $chunks;

        return $this;
    }

    public function length(int $length): static
    {
        $this->length = $length;

        return $this;
    }

    public function spacer(string $spacer): static
    {
        $this->spacer = $spacer;

        return $this;
    }

    public function generate(): string
    {
        $random = $this->make();
        $this->generated[] = $random;

        return $random;
    }

    /**
     * @throws UniqueThresholdReachedException
     */
    public function unique(): string
    {
        for ($i = 0; $i < $this->uniqueCollisionThreshold; $i++) {
            $random = $this->make();

            if (!in_array($random, $this->generated, true)) {
                $this->generated[] = $random;

                return $random;
            }

            $this->collisions++;
        }

        throw new UniqueThresholdReachedException(
            sprintf('Maximum attempts (%s) at creating a new unique value reached', $this->uniqueCollisionThreshold)
        );
    }

    public function collisions(): int
    {
        return $this->collisions;
    }

    public function reset(): static
    {
        $this->collisions = 0;
        $this->generated = [];

        return $this;
    }

    public function permutations(): int
    {
        return strlen($this->driver->digest()) ** $this->length;
    }

    private function make(): string
    {
        return $this->generator->make($this->length, $this->chunks, $this->spacer);
    }
}
