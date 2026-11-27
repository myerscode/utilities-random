<?php

declare(strict_types=1);

namespace Myerscode\Utilities\Random;

use Myerscode\Utilities\Random\Constraints\ConstraintInterface;
use Myerscode\Utilities\Random\Constraints\OutputConstraint;
use Myerscode\Utilities\Random\Constraints\PoolConstraint;
use Myerscode\Utilities\Random\Drivers\CustomDriver;
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

    /** Bytes consumed per sample: 1, 2 or 4, so pools longer than 256 chars still sample uniformly. */
    private int $sampleBytes = 1;

    /**
     * Whether the satisfiability check has passed for the current pool/constraints.
     * Reset to false whenever pool or constraints change.
     */
    private bool $satisfiabilityChecked = false;

    private int $validationAttempts = 100;

    public function __construct(protected readonly RandomDriverInterface $driver)
    {
        $this->loadDriverPool();
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
     * Reload the pool from the driver, e.g. after the driver was re-seeded.
     *
     * @throws EmptyPoolException
     */
    public function refresh(): void
    {
        $this->loadDriverPool();
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

        $this->loadDriverPool();
    }

    /**
     * @throws EmptyPoolException
     */
    public function setPool(string $pool): void
    {
        $this->applyUnits($this->splitUnits($this->applyPoolConstraints($pool)));
    }

    /**
     * Number of distinct characters in the current pool.
     */
    public function uniquePoolSize(): int
    {
        return count(array_unique($this->poolArray));
    }

    private function applyPoolConstraints(string $pool): string
    {
        foreach ($this->poolConstraints as $poolConstraint) {
            $pool = $poolConstraint->filter($pool);
        }

        return $pool;
    }

    /**
     * @param  array<int, string>  $units
     *
     * @throws EmptyPoolException
     */
    private function applyUnits(array $units): void
    {
        $this->pool = implode('', $units);
        $this->poolLength = count($units);
        $this->satisfiabilityChecked = false;

        if ($this->poolLength === 0) {
            throw new EmptyPoolException(
                'The character pool is empty after applying pool constraints. Check your driver and constraint combination.',
            );
        }

        // Pre-compute pool array and lookup metadata
        $this->poolArray = $units;
        $this->sampleBytes = $this->poolLength <= 256 ? 1 : ($this->poolLength <= 65536 ? 2 : 4);
        $range = 256 ** $this->sampleBytes;
        $this->poolThreshold = $range - ($range % $this->poolLength);
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
        $width = $this->sampleBytes;
        $requestSize = max(1, ((int) ceil($length * (256 ** $width) / $threshold) + 8) * $width);

        $result = [];
        $pos = 0;

        while ($pos < $length) {
            $bytes = random_bytes($requestSize);

            // One byte per sample: read bytes directly instead of allocating an unpack() array.
            if ($width === 1) {
                for ($i = 0, $n = strlen($bytes); $i < $n; $i++) {
                    $value = ord($bytes[$i]);

                    if ($value < $threshold) {
                        $result[$pos++] = $poolArray[$value % $poolLength];

                        if ($pos === $length) {
                            break;
                        }
                    }
                }

                continue;
            }

            /** @var array<int, int> $values */
            $values = unpack($width === 2 ? 'n*' : 'N*', $bytes);

            foreach ($values as $value) {
                if ($value < $threshold) {
                    $result[$pos++] = $poolArray[$value % $poolLength];

                    if ($pos === $length) {
                        break;
                    }
                }
            }
        }

        return implode('', $result);
    }

    /**
     * Load the driver's pool. A CustomDriver exposes its entries as units so multi-character
     * entries are sampled whole; pool constraints are then applied to each unit.
     *
     * @throws EmptyPoolException
     */
    private function loadDriverPool(): void
    {
        if (!$this->driver instanceof CustomDriver) {
            $this->setPool($this->driver->digest());

            return;
        }

        $units = array_values(array_filter(
            $this->driver->units(),
            fn (string $unit): bool => array_all($this->poolConstraints, fn ($c) => $c->filter($unit) === $unit),
        ));

        $this->applyUnits($units);
    }

    private function passesOutputConstraints(string $value): bool
    {
        return array_all($this->outputConstraints, fn ($constraint) => $constraint->passes($value));
    }

    /**
     * Split a pool into characters. UTF-8 pools are split by code point, anything else by byte.
     *
     * @return array<int, string>
     */
    private function splitUnits(string $pool): array
    {
        return $pool !== '' && mb_check_encoding($pool, 'UTF-8') ? mb_str_split($pool) : str_split($pool);
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
