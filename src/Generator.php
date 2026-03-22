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

    /**
     * Whether the satisfiability check has passed for the current pool/constraints.
     * Reset to false whenever pool or constraints change.
     */
    private bool $satisfiabilityChecked = false;

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
        $this->satisfiabilityChecked = false;

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

        if (!$this->satisfiabilityChecked) {
            $this->validateConstraintSatisfiability($totalLength);
            $this->satisfiabilityChecked = true;
        }

        $hasConstraints = $this->outputConstraints !== [];

        for ($attempt = 0; $attempt < $this->validationAttempts; $attempt++) {
            $result = $this->buildString($chunkLength, $numChunks, $spacer);

            if (!$hasConstraints || $this->passesOutputConstraints($result)) {
                return $result;
            }
        }

        throw new ValidationThresholdReachedException(
            sprintf('Maximum attempts (%s) at generating a valid string reached', $this->validationAttempts),
        );
    }

    /**
     * Build a random string using batched random byte generation.
     * Uses random_bytes() once per chunk instead of random_int() per character,
     * with rejection sampling to avoid modulo bias.
     */
    private function buildString(int $chunkLength, int $numChunks, string $spacer): string
    {
        // Single chunk fast path — skip array/implode overhead
        if ($numChunks === 1) {
            return $this->generateChunk($chunkLength);
        }

        $chunks = [];

        for ($i = 0; $i < $numChunks; $i++) {
            $chunks[] = $this->generateChunk($chunkLength);
        }

        return implode($spacer, $chunks);
    }

    /**
     * Generate a single chunk of random characters from the pool.
     * Uses random_bytes() in bulk and maps bytes to pool indices via rejection
     * sampling — bytes that would cause modulo bias are discarded.
     */
    private function generateChunk(int $length): string
    {
        $pool = $this->pool;
        $poolLength = $this->poolLength;

        // Rejection threshold: largest multiple of poolLength that fits in a byte.
        // Bytes >= this value are discarded to eliminate modulo bias.
        $threshold = 256 - (256 % $poolLength);

        $result = '';
        $remaining = $length;

        while ($remaining > 0) {
            // Request extra bytes to account for rejections
            $bytes = random_bytes($remaining + 16);
            $byteCount = strlen($bytes);

            for ($i = 0; $i < $byteCount && $remaining > 0; $i++) {
                $byte = ord($bytes[$i]);

                if ($byte < $threshold) {
                    $result .= $pool[$byte % $poolLength];
                    $remaining--;
                }
            }
        }

        return $result;
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
