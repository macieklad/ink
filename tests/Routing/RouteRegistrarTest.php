<?php

use Ink\Routing\Router;
use Ink\Routing\RouteRegistrar;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class RouteRegistrarTest extends MockeryTestCase 
{
    public function setUp(): void
    {
        $this->router = Mockery::mock(Router::class);
        $this->registrar = new RouteRegistrar($this->router);
    }

    /**
     * Check if registrar passes methods with the same name
     * as one request types to the router
     *
     * @return void
     */
    public function testRequestMethodCallMap()
    {
        $params = ['uri', 'action'];
        $methods = ['get', 'post', 'patch', 'delete'];


        foreach ($methods as $method) {
            $this->router 
                ->shouldReceive($method)
                ->withAnyArgs()
                ->once();
        }
        
        foreach ($methods as $method) {
            $this->registrar->$method(...$params);
        }
    }

    /**
     * Check if group call is passed to the router
     *
     * @return void
     */
    public function testRouteGrouping()
    {
        $this->router->shouldReceive('loadRoutes')
            ->with('routes.php', [])
            ->once();

        $this->registrar->group('routes.php');
    }

    /**
     * Check if all allowed attributes are properly assigned while calling 
     * registrar through magic methods
     *
     * @return void
     */
    public function testRegistrarAttributesAreSet()
    {
        $allowed = ['prefix', 'module'];

        foreach ($allowed as $attribute) {
            $this->registrar->$attribute($attribute);
        }
        $this->assertSame([
            'prefix' => 'prefix',
            'module' => 'module'
        ], $this->getProperty($this->registrar, 'attributes'));
    }

    /**
     * Check if calling non existent method on registrar fails
     * 
     * @expectException \InvalidArgumentException
     * @return void
     */
    public function testInvalidMethodCall()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->registrar->nonExistent();
    }

    /**
     * Get protected or private property
     *
     * @param $object
     * @param $propertyName
     * @return mixed
     */
    function getProperty($object, $propertyName)
    {
        $reflection = new ReflectionClass($object);
        $property = $reflection->getProperty($propertyName);
        $property->setAccessible(true);
        return $property->getValue($object);
    }
}