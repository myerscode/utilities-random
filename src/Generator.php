<?php

declare(strict_types=1);

namespace Myerscode\Utilities\Random;

use Myerscode\Utilities\Random\Constraints\ConstraintInterface;
use Myerscode\Utilities\Random\Constraints\OutputConstraint;
use Myerscode\Utilities\Random\Constraints\PoolConstraint;
use Myerscode\Utilities\Random\Drivers\RandomDriverInterface;
use Myerscode\Utilities\Random\Exceptions\EmptyPoolException;
use Myerscode\Utilities\Random\Exceptions\UnsatisfiableConstraintException;
use Myerscode\Utilities\Random\Exceptions\ValidationThresholdReachedException;

class Generator
{
    private string $pool;

    private int $poolLength;

    /** @var array<int, PoolConstraint> */
    private array $poolConstraints = [];

    /** @var array<int, OutputConstraint> */
    private array $outputConstraints = [];

    private int $validationAttempts = 100;

    public function __construct(protected readonly RandomDriverInterface $driver)
    {
        $this->setPool($this->driver->digest());
    }

    /**
     * @throws EmptyPoolException
     */
    public function setPool(string $pool): void
    {
        $this->pool = $this->applyPoolConstraints($pool);
        $this->poolLength = strlen($this->pool);

        if ($this->poolLength === 0) {
            throw new EmptyPoolException(
                'The character pool is empty after applying pool constraints. Check your driver and constraint combination.',
            );
        }
    }

    public function getPool(): string
    {
        return $this->pool;
    }

    /**
     * @param  array<int, ConstraintInterface>  $constraints
     *
     * @throws EmptyPoolException
     */
    public function setConstraints(array $constraints): void
    {
        $this->poolConstraints = [];
        $this->outputConstraints = [];

        foreach ($constraints as $constraint) {
            if ($constraint instanceof PoolConstraint) {
                $this->poolConstraints[] = $constraint;
            }

            if ($constraint instanceof OutputConstraint) {
                $this->outputConstraints[] = $constraint;
            }
        }

        $this->setPool($this->driver->digest());
    }

    /**
     * @return array<int, ConstraintInterface>
     */
    public function getConstraints(): array
    {
        return [...$this->poolConstraints, ...$this->outputConstraints];
    }

    /**
     * @throws ValidationThresholdReachedException
     * @throws UnsatisfiableConstraintException
     */
    public function make(int $chunkLength = 4, int $numChunks = 1, string $spacer = ''): string
    {
        $chunkLength = max(1, $chunkLength);

        if ($numChunks <= 1) {
            $numChunks = 1;
            $spacer = '';
        }

        $totalLength = ($chunkLength * $numChunks) + (max(0, $numChunks - 1) * strlen($spacer));
        $this->validateConstraintSatisfiability($totalLength);

        for ($attempt = 0; $attempt < $this->validationAttempts; $attempt++) {
            $result = $this->buildString($chunkLength, $numChunks, $spacer);

            if ($this->passesOutputConstraints($result)) {
                return $result;
            }
        }

        throw new ValidationThresholdReachedException(
            sprintf('Maximum attempts (%s) at generating a valid string reached', $this->validationAttempts),
        );
    }

    private function buildString(int $chunkLength, int $numChunks, string $spacer): string
    {
        $chunks = [];

        for ($x = 0; $x < $numChunks; $x++) {
            $chunk = '';

            for ($y = 0; $y < $chunkLength; $y++) {
                $chunk .= $this->pool[random_int(0, $this->poolLength - 1)];
            }

            $chunks[] = $chunk;
        }

        return implode($spacer, $chunks);
    }

    private function passesOutputConstraints(string $value): bool
    {
        foreach ($this->outputConstraints as $constraint) {
            if (!$constraint->passes($value)) {
                return false;
            }
        }

        return true;
    }

    private function applyPoolConstraints(string $pool): string
    {
        foreach ($this->poolConstraints as $constraint) {
            $pool = $constraint->filter($pool);
        }

        return $pool;
    }

    /**
     * Run each output constraint's canBeSatisfiedBy check against the current pool.
     * This catches impossible constraint/driver combinations early (e.g. MustContainLetter
     * with a NumericDriver) instead of silently burning through retry attempts.
     *
     * @throws UnsatisfiableConstraintException
     */
    private function validateConstraintSatisfiability(int $length): void
    {
        foreach ($this->outputConstraints as $constraint) {
            if (!$constraint->canBeSatisfiedBy($this->pool, $length)) {
                throw new UnsatisfiableConstraintException(
                    sprintf(
                        'Constraint [%s] can never be satisfied by the current character pool.',
                        $constraint::class,
                    ),
                );
            }
        }
    }
}
