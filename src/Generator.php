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
    /** @var array<int, OutputConstraint> */
    private array $outputConstraints = [];
    private string $pool;

    /** @var array<int, string> Pre-computed pool as array for faster indexed lookup */
    private array $poolArray = [];

    /** @var array<int, PoolConstraint> */
    private array $poolConstraints = [];

    private int $poolLength;

    /**
     * Cached rejection threshold: highest multiple of poolLength that fits in a byte.
     * Bytes >= this value are discarded to avoid modulo bias.
     */
    private int $poolThreshold = 256;

    /**
     * Whether the satisfiability check has passed for the current pool/constraints.
     * Reset to false whenever pool or constraints change.
     */
    private bool $satisfiabilityChecked = false;

    private int $validationAttempts = 100;

    public function __construct(protected readonly RandomDriverInterface $driver)
    {
        $this->setPool($this->driver->digest());
    }

    /**
     * @return array<int, ConstraintInterface>
     */
    public function getConstraints(): array
    {
        return [...$this->poolConstraints, ...$this->outputConstraints];
    }

    public function getPool(): string
    {
        return $this->pool;
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

        // Inlined fast path: single chunk, no output constraints
        // Skips method call overhead for the most common usage pattern
        if ($numChunks === 1 && $this->outputConstraints === []) {
            return $this->generateChunk($chunkLength);
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

        // Pre-compute pool array and lookup metadata
        $this->poolArray = str_split($this->pool);
        $this->poolThreshold = 256 - (256 % $this->poolLength);
    }

    private function applyPoolConstraints(string $pool): string
    {
        foreach ($this->poolConstraints as $poolConstraint) {
            $pool = $poolConstraint->filter($pool);
        }

        return $pool;
    }

    /**
     * Build a random string using batched random byte generation.
     * Uses random_bytes() once per chunk instead of random_int() per character,
     * with rejection sampling to avoid modulo bias.
     */
    private function buildString(int $chunkLength, int $numChunks, string $spacer): string
    {
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
     * Uses random_bytes() in bulk with pre-cached pool array and rejection threshold.
     * Bytes above the threshold are discarded to avoid modulo bias.
     */
    private function generateChunk(int $length): string
    {
        $poolArray = $this->poolArray;
        $poolLength = $this->poolLength;
        $threshold = $this->poolThreshold;

        // Over-request to cover expected rejections in one pass
        $requestSize = max(1, (int) ceil($length * 256 / $threshold) + 8);

        $result = str_repeat("\0", $length);
        $pos = 0;

        while ($pos < $length) {
            $values = unpack('C*', random_bytes($requestSize));
            /** @var array<int, int> $values */

            foreach ($values as $value) {
                if ($value < $threshold) {
                    $result[$pos++] = $poolArray[$value % $poolLength];

                    if ($pos === $length) {
                        break;
                    }
                }
            }
        }

        return $result;
    }

    private function passesOutputConstraints(string $value): bool
    {
        return array_all($this->outputConstraints, fn ($constraint) => $constraint->passes($value));
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
        foreach ($this->outputConstraints as $outputConstraint) {
            if (!$outputConstraint->canBeSatisfiedBy($this->pool, $length)) {
                throw new UnsatisfiableConstraintException(
                    sprintf(
                        'Constraint [%s] can never be satisfied by the current character pool.',
                        $outputConstraint::class,
                    ),
                );
            }
        }
    }
}
