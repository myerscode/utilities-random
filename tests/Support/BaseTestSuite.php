<?php

namespace Tests\Support;

use Myerscode\Utilities\Random\Utility;
use PHPUnit\Framework\TestCase;

abstract class BaseTestSuite extends TestCase
{

    /**
     * Get the utility being tested
     *
     * @param $config
     * @return Utility
     */
    public function utility($config)
    {
        return new Utility($config);
    }
}
