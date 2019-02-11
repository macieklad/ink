<?php

use Ink\Routing\Route;
use Ink\Routing\Router;
use Mockery\Adapter\Phpunit\MockeryTestCase;

function add_action()
{
    $args = func_get_args();

    RouterTest::$functions->add_action(...$args);
}

class RouterTest extends MockeryTestCase
{
    /**
     * Undocumented variable
     *
     * @var [type]
     */
    public static $functions;

    public function setUp(): void
    {
        static::$functions = Mockery::spy();
    }

    public function testRouterWordpressHook()
    {
        $router = new Router;
        $route = Mockery::mock(Route::class);
        $router->registerRoute($route);

        static::$functions->shouldHaveReceived('add_action')->once();
    }

}