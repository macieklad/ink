<?php

namespace Tests;

use Mockery;

trait MocksGlobals
{
    /**
     * "Clear" globals, by resetting mockery assertions
     *
     * @return void
     */
    public function clearGlobals()
    {
        TestHelpers::$functions = Mockery::mock();
    }
}