<?php

declare(strict_types=1);

namespace Tests;

use Myerscode\Utilities\Random\Exceptions\InvalidProviderException;
use Myerscode\Utilities\Random\Utility;
use PHPUnit\Framework\TestCase;

abstract class BaseTestSuite extends TestCase
{
    /**
     * @throws InvalidProviderException
     */
    public function utility(string $config): Utility
    {
        return new Utility($config);
    }
}
