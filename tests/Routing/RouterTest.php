<?php

use Tests\TestHelpers;
use Ink\Routing\Route;
use Ink\Routing\Router;
use Tests\Routing\StubController;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Ink\Container\ContainerProxy as Container;
use function DI\create;

function add_action()
{
    $args = func_get_args();

    call_user_func($args[1]);

    RouterTest::$functions->add_action(...$args);
}

function register_rest_route()
{
    $args = func_get_args();

    RouterTest::$functions->register_rest_route(...$args);
}

class RouterTest extends MockeryTestCase
{
    /**
     * Controllers test namespace
     *
     * @var string
     */
    protected static $controllerTestNamespace = 'Tests\Routing';

    /**
     * Name of the mocked controller
     *
     * @var string
     */
    protected static $controllerTestName = 'StubController';

    /**
     * Method which will be called on mocked controller
     *
     * @var string
     */
    protected static $controllerTestMethod = 'handler';

    /**
     * Undocumented variable
     *
     * @var Mockery\MockInterface
     */
    public static $functions;

    public function setUp(): void
    {
        $this->router = new Router;
    }

    /**
     * Test if all basic method calls work properly. Route
     *  should be constructed properly, and its action
     *  have to be compiled and callable
     *
     * @return void
     */
    public function testBasicRoutingFunctionCreateRoutes()
    {
        $methods = ['get', 'post', 'patch', 'delete'];
        $uri = 'foo';
        $action = static::mockedController() . '@' . static::$controllerTestMethod;

        foreach ($methods as $method) {
            $this->router->{$method}($uri, $action);
        }

        $routes = $this->router->routes();

        $this->assertEquals(count($methods), count($routes));
        
        foreach ($methods as $key => $method) {
            $this->assertSame([ strtoupper($method) ], $routes[$key]->methods);
            $this->assertSame($uri, $routes[$key]->uri);
            $this->assertSame($action, $routes[$key]->action);
        }
    }
    
    /**
     * Check if router sets controller namespace prefix correctly 
     *
     * @return void
     */
    public function testControllerNamespaceSetting()
    {
        $namespace = 'Theme\Http\Controllers';
        $router = Mockery::mock(Router::class);

        $this->assertSame('', TestHelpers::getProperty($this->router, 'controllerNamespace'));
        $this->router->setControllerNamespace('Theme\Http\Controllers');
        $this->assertSame($namespace, TestHelpers::getProperty($this->router, 'controllerNamespace'));
    }

    /**
     * Test if route is compiling properly with controller string passed
     *
     * @return void
     */
    public function testRouteStringActionCompilation()
    {
        $callback = $this->router->compileAction(static::mockedController() . '@handler');

        $this->assertTrue($callback instanceof Closure);
        $this->assertSame('response', $callback());
    }   


    /**
     * Test if action compilation is successfull when callback
     * is passed
     *
     * @return void
     */
    public function testRouteCallbackActionCompilation() 
    {
        $action = $this->router->compileAction(function() {
            return 'callback_response';
        });

        $this->assertSame('callback_response', $action());
    }

    /**
     * Test if router passes request to action callback
     *
     * @return void
     */
    public function testRouteActionCompilationWithRequest()
    {   
        $action = $this->router->compileAction(function($req) {
            return 'hello_' . $req['world'];
        });

        $this->assertSame('hello_world', $action(['world' => 'world']));
    }
    
    /**
     * Test if router fails compilation when providing invalid argument types
     *
     * @return void
     */
    public function testRouteInvalidActionTypeCompilationFail()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->router->compileAction([]);
    }

    /**
     * Check if route compilation fails with non existent class argument
     *
     * @return void
     */
    public function testRouteStringActionCompilationFail()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->router->compileAction('NoneExistentClazz@foo');
    }

    /**
     * Test if action compilation fails without provided action,
     * but with proper controller passed as string
     *
     * @return void
     */
    public function testRouteActionCompilationFailWithoutAction()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->router->compileAction(static::mockedController());
    }

    /**
     * Test if router loads routes from callback and files
     *
     * @return void
     */
    public function testRouteLoading()
    {
        $this->router->loadRoutes(__DIR__ . DIRECTORY_SEPARATOR . 'testRoutes.php');
        $routes = $this->router->routes();
        $lastRoute = $routes[1];

        $this->assertEquals(2, count($routes));
        $this->assertSame('/foo/{baz}', $lastRoute->uri);
        $this->assertEquals(1, count($lastRoute->params));
    }


    /** 
     * Test if wordpress hooks are called with correct arguments 
     * while registering route
     * 
     * @return void
     */
    public function testRouterWordpressHook()
    {
        $route = Mockery::mock(Route::class)->makePartial();
        static::$functions = Mockery::mock();

        $route->methods = ['GET'];
        $route->uri = '';
        $route->action = function () {};

        static::$functions
            ->shouldReceive('register_rest_route')
            ->once()
            ->with('v1', '', Mockery::on(function($arg) use ($route) {
                $hasProperMethods = $arg['methods'] === $route->methods;
                $hasSameCallback = $this->router->compileAction($route->action)() === $arg['callback']();

                return $hasProperMethods && $hasSameCallback;
            }));

        static::$functions
            ->shouldReceive('add_action')
            ->once()
            ->with('rest_api_init', Mockery::any());


        $this->router->addRoute($route);
        $this->router->listen();

    }


    /**
     * Return mocked controller full namespace with name
     *
     * @return void
     */
    public static function mockedController()
    {
        return static::$controllerTestNamespace . '\\' . static::$controllerTestName;
    }
}