<?php

declare(strict_types=1);

namespace Myerscode\Utilities\Random\Constraints\Pool;

/**
 * Removes visually similar characters from the pool (e.g. oO0, I1l).
 * Extends ExcludeCharacters with a sensible default character set.
 */
class ExcludeSimilarCharacters extends ExcludeCharacters
{
    public function __construct()
    {
        parent::__construct(['o', 'O', '0', 'I', '1', 'l']);
    }
}
