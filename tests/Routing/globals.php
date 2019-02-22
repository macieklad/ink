<?php

namespace Ink\Routing;

use Tests\TestHelpers;
use Tests\Routing\RouterTest;

/**
 * Mock wordpress add_action
 *
 * @return void
 */
function add_action()
{
    $args = func_get_args();

    call_user_func($args[1]);

    TestHelpers::passGlobalCall('add_action', $args);
}


/**
 * Mock wordpress register_rest_route
 *
 * @return void
 */
function register_rest_route()
{
    $args = func_get_args();

    TestHelpers::passGlobalCall('register_rest_route', $args);
}
