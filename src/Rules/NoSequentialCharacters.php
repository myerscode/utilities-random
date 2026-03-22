<?php

declare(strict_types=1);

namespace Myerscode\Utilities\Random\Rules;

/**
 * Rejects strings containing sequential character runs (e.g. abc, 123, CBA, 321).
 */
class NoSequentialCharacters implements ValidationRule
{
    public function __construct(private readonly int $length = 3) {}

    public function passes(string $value): bool
    {
        $chars = strlen($value);

        if ($chars < $this->length) {
            return true;
        }

        for ($i = 0; $i <= $chars - $this->length; $i++) {
            if ($this->isSequential(substr($value, $i, $this->length))) {
                return false;
            }
        }

        return true;
    }

    /**
     * Sequential detection depends on output ordering, not pool contents,
     * so this always returns true — we can't predict it upfront.
     */
    public function canBeSatisfiedBy(string $pool, int $length): bool
    {
        return true;
    }

    private function isSequential(string $segment): bool
    {
        $ascending = true;
        $descending = true;
        $length = strlen($segment);

        for ($i = 1; $i < $length; $i++) {
            if (ord($segment[$i]) !== ord($segment[$i - 1]) + 1) {
                $ascending = false;
            }

            if (ord($segment[$i]) !== ord($segment[$i - 1]) - 1) {
                $descending = false;
            }

            if (!$ascending && !$descending) {
                return false;
            }
        }

        return true;
    }
}
