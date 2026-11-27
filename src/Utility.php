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
    private const int MAX_UNIQUE_ATTEMPTS = 1_000_000;

    private int $chunks = 0;

    private int $collisions = 0;

    /** @var array<int|string, true> Hash set of produced values, keyed by value for O(1) lookup. */
    private array $generated = [];

    private readonly Generator $generator;

    private int $length = 5;

    private readonly RandomDriverInterface $randomDriver;

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
        $this->generated[$random] = true;

        return $random;
    }

    public function length(int $length): static
    {
        $this->length = $length;

        return $this;
    }

    /**
     * Number of distinct strings that can be generated: unique pool characters
     * raised to the total output length (length x chunks, spacers excluded).
     * Returns a float once the value exceeds PHP_INT_MAX.
     */
    public function permutations(): int|float
    {
        return $this->generator->uniquePoolSize() ** ($this->length * max(1, $this->chunks));
    }

    public function reset(): static
    {
        $this->collisions = 0;
        $this->generated = [];

        return $this;
    }

    /**
     * Re-seed the driver and reload the generator's pool from it. The pool's characters stay the
     * same; output randomness comes from random_bytes() at generation time, not from pool order.
     */
    public function seed(): void
    {
        $this->randomDriver->seed();
        $this->generator->refresh();
    }

    public function spacer(string $spacer): static
    {
        $this->spacer = $spacer;

        return $this;
    }

    /**
     * Generate a value this utility has not produced before (via generate() or unique()) since
     * the last reset(). Gives up after a bounded number of attempts that scales with how full
     * the permutation space is, so an exhausted space throws instead of looping.
     *
     * @throws UniqueThresholdReachedException
     */
    public function unique(): string
    {
        $space = $this->permutations();
        $remaining = max(1, $space - count($this->generated));
        // 20 x the expected attempts to hit a free value: a miss on a free slot is about e^-20.
        $attempts = is_finite($space) ? (int) min(self::MAX_UNIQUE_ATTEMPTS, max(20, ceil(20 * $space / $remaining))) : 20;

        for ($i = 0; $i < $attempts; $i++) {
            $random = $this->make();

            if (!isset($this->generated[$random])) {
                $this->generated[$random] = true;

                return $random;
            }

            $this->collisions++;
        }

        throw new UniqueThresholdReachedException(
            sprintf('Maximum attempts (%s) at creating a new unique value reached', $attempts),
        );
    }

    private function make(): string
    {
        return $this->generator->make($this->length, $this->chunks, $this->spacer);
    }
}
