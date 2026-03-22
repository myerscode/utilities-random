<?php

declare(strict_types=1);

namespace Myerscode\Utilities\Random;

use Myerscode\Utilities\Random\Drivers\RandomDriverInterface;
use Myerscode\Utilities\Random\Exceptions\EmptyPoolException;
use Myerscode\Utilities\Random\Exceptions\UnsatisfiableRuleException;
use Myerscode\Utilities\Random\Exceptions\ValidationThresholdReachedException;
use Myerscode\Utilities\Random\Rules\PoolRule;
use Myerscode\Utilities\Random\Rules\RuleInterface;
use Myerscode\Utilities\Random\Rules\ValidationRule;

class Generator
{
    private string $pool;

    private int $poolLength;

    /** @var array<int, PoolRule> */
    private array $poolRules = [];

    /** @var array<int, ValidationRule> */
    private array $validationRules = [];

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
        $this->pool = $this->applyPoolRules($pool);
        $this->poolLength = strlen($this->pool);

        if ($this->poolLength === 0) {
            throw new EmptyPoolException(
                'The character pool is empty after applying pool rules. Check your driver and rule combination.',
            );
        }
    }

    public function getPool(): string
    {
        return $this->pool;
    }

    /**
     * @param  array<int, RuleInterface>  $rules
     *
     * @throws EmptyPoolException
     */
    public function setRules(array $rules): void
    {
        $this->poolRules = [];
        $this->validationRules = [];

        foreach ($rules as $rule) {
            if ($rule instanceof PoolRule) {
                $this->poolRules[] = $rule;
            }

            if ($rule instanceof ValidationRule) {
                $this->validationRules[] = $rule;
            }
        }

        $this->setPool($this->driver->digest());
    }

    /**
     * @return array<int, RuleInterface>
     */
    public function getRules(): array
    {
        return [...$this->poolRules, ...$this->validationRules];
    }

    /**
     * @throws ValidationThresholdReachedException
     * @throws UnsatisfiableRuleException
     */
    public function make(int $chunkLength = 4, int $numChunks = 1, string $spacer = ''): string
    {
        $chunkLength = max(1, $chunkLength);

        if ($numChunks <= 1) {
            $numChunks = 1;
            $spacer = '';
        }

        $totalLength = ($chunkLength * $numChunks) + (max(0, $numChunks - 1) * strlen($spacer));
        $this->validateRuleSatisfiability($totalLength);

        for ($attempt = 0; $attempt < $this->validationAttempts; $attempt++) {
            $result = $this->buildString($chunkLength, $numChunks, $spacer);

            if ($this->passesValidation($result)) {
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

    private function passesValidation(string $value): bool
    {
        foreach ($this->validationRules as $rule) {
            if (!$rule->passes($value)) {
                return false;
            }
        }

        return true;
    }

    private function applyPoolRules(string $pool): string
    {
        foreach ($this->poolRules as $rule) {
            $pool = $rule->filter($pool);
        }

        return $pool;
    }

    /**
     * Run each validation rule's canBeSatisfiedBy check against the current pool.
     * This catches impossible rule/driver combinations early (e.g. MustContainLetter
     * with a NumericDriver) instead of silently burning through retry attempts.
     *
     * @throws UnsatisfiableRuleException
     */
    private function validateRuleSatisfiability(int $length): void
    {
        foreach ($this->validationRules as $rule) {
            if (!$rule->canBeSatisfiedBy($this->pool, $length)) {
                throw new UnsatisfiableRuleException(
                    sprintf(
                        'Rule [%s] can never be satisfied by the current character pool.',
                        $rule::class,
                    ),
                );
            }
        }
    }
}
