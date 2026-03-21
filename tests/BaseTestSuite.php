<?php

namespace Tests;

use Myerscode\Utilities\Random\Exceptions\InvalidProviderException;
use Myerscode\Utilities\Random\Utility;
use PHPUnit\Framework\TestCase;

abstract class BaseTestSuite extends TestCase
{
    /**
     * Get the utility being tested
     *
     * @param $config
     *
     * @throws InvalidProviderException
     */
    public function utility($config): Utility
    {
        return new Utility($config);
    }
}
