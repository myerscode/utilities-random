<?php

declare(strict_types=1);

namespace Myerscode\Utilities\Random;

use Myerscode\Utilities\Random\Constraints\ConstraintInterface;
use Myerscode\Utilities\Random\Drivers\RandomDriverInterface;
use Myerscode\Utilities\Random\Exceptions\InvalidConstraintException;
use Myerscode\Utilities\Random\Exceptions\InvalidProviderException;
use Myerscode\Utilities\Random\Exceptions\UniqueThresholdReachedException;

class Utility
{
    private int $chunks = 0;

    private int $collisions = 0;

    /** @var array<int, string> */
    private array $generated = [];

    private readonly Generator $generator;

    private int $length = 5;
    private readonly RandomDriverInterface $randomDriver;

    private string $spacer = '-';

    private int $uniqueCollisionThreshold = 10;

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

        $this->randomDriver = $provider;
        $this->generator = new Generator($this->randomDriver);
    }

    public function chunks(int $chunks): static
    {
        $this->chunks = $chunks;

        return $this;
    }

    public function collisions(): int
    {
        return $this->collisions;
    }

    /**
     * Apply constraints to the generator. Accepts class names or instances.
     *
     * @param  array<int, ConstraintInterface|string>  $constraints
     *
     * @throws InvalidConstraintException
     */
    public function constraints(array $constraints): static
    {
        $resolved = [];

        foreach ($constraints as $constraint) {
            if (is_string($constraint)) {
                if (!class_exists($constraint)) {
                    throw new InvalidConstraintException(sprintf('Constraint class [%s] does not exist', $constraint));
                }

                $instance = new $constraint();

                if (!$instance instanceof ConstraintInterface) {
                    throw new InvalidConstraintException(sprintf('Constraint [%s] must implement ConstraintInterface', $constraint));
                }

                $resolved[] = $instance;
            } else {
                $resolved[] = $constraint;
            }
        }

        $this->generator->setConstraints($resolved);

        return $this;
    }

    public function generate(): string
    {
        $random = $this->make();
        $this->generated[] = $random;

        return $random;
    }

    public function length(int $length): static
    {
        $this->length = $length;

        return $this;
    }

    public function permutations(): int
    {
        return strlen((string) $this->randomDriver->digest()) ** $this->length;
    }

    public function reset(): static
    {
        $this->collisions = 0;
        $this->generated = [];

        return $this;
    }

    public function seed(): void
    {
        $this->randomDriver->seed();
    }

    public function spacer(string $spacer): static
    {
        $this->spacer = $spacer;

        return $this;
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
            sprintf('Maximum attempts (%s) at creating a new unique value reached', $this->uniqueCollisionThreshold),
        );
    }

    private function make(): string
    {
        return $this->generator->make($this->length, $this->chunks, $this->spacer);
    }
}
