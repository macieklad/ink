<?php

namespace Tests;

use Mockery;

trait MocksGlobals
{
    /**
     * Mock class that receives global function calls
     *
     * @var Mockery\MockInterface
     */
    public static $functions;

    /**
     * "Clear" globals, by resetting mockery assertions
     *
     * @return void
     */
    public function clearGlobals()
    {
        static::$functions = Mockery::mock();
    }
}