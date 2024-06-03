<?php
/**
 * Created by PhpStorm.
 * User: Fred
 * Date: 21/03/2018
 * Time: 21:15
 */

namespace Myerscode\Utilities\Random\Drivers;


abstract class AbstractDriver
{

    /**
     * @var int
     */
    protected $iterations = 5000;

    /**
     * @var string
     */
    protected $digest;


    /**
     * @return string
     */
    public function digest(): string
    {
        return $this->digest;
    }
}