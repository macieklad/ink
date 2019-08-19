<?php

use Ink\Routing\Route;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class RouteTest extends MockeryTestCase
{

    /**
     * Test basic fields contents set in constructor
     *
     * @return void
     */
    public function testRouteInitialization()
    {
        $methods = ['GET', 'POST'];
        $uri = 'foo';
        $route = $this->makeTestRoute(
            [
            'methods' => $methods,
            'uri' => $uri
            ]
        );

        $this->assertEquals($methods, $route->methods);
        $this->assertEquals($uri, $route->uri);
    }

    /**
     * Chech if params are extracted properly
     *
     * @return void
     */
    public function testRouteParamExtraction()
    {
        $uri = "/foo/{bar}/{baz}";
        $route = $this->makeTestRoute([ 'uri' => $uri ]);

        $route->extractParams();

        $this->assertEquals(['bar', 'baz'], $route->params);
    }

    /**
     * Test whether routes are properly compiled
     *
     * @return void
     */
    public function testRouteCompilation()
    {
        $uri = "/foo/{bar}/{baz}?query=xxx";
        $expectation = "/foo/(?P<bar>[a-zA-Z\\d_-]+)" .
                       "/(?P<baz>[a-zA-Z\\d_-]+)?query=xxx";
        $route = $this->makeTestRoute(['uri' => $uri ]);

        $route->compile();
        
        $this->assertSame($expectation, $route->wpUri);
    }

    /**
     * Test if prefix method properly constructs route uri
     *
     * @return void
     */
    public function testPrefixSetup()
    {
        $uri = "/foo";
        $route = $this->makeTestRoute(['uri' => $uri]);

        $route->prefix('bar');
        $this->assertSame('/bar/foo', $route->uri);
        $route->prefix('/baz');
        $this->assertSame('/baz/bar/foo', $route->uri);
    }

    /**
     * Make sure that route module is changed correctly
     *
     * @return void
     */
    public function testModuleChange()
    {
        $route = $this->makeTestRoute();

        $route->module('foo');
        $this->assertSame('foo', $route->module);
    }
    
    /**
     * Tests if attributes passed as array are merged into route
     *
     * @group  dev
     * @return void
     */
    public function testAttributeMethodCall()
    {
        $args = array_values($this->defaultRouteArgs());
        $mock = Mockery::mock(Route::class, $args)
            ->makePartial();

        $mock->shouldReceive('prefix')
            ->with('bar')
            ->once();

        $mock->shouldReceive('module')
            ->with('stamp/v1')
            ->once();
        
        $mock->mergeAttributes(
            [
            'prefix' => 'bar',
            'module' => 'stamp/v1'
            ]
        );
    }

    /**
     * Test if passing action as attributes sets route action
     *
     * @return void
     */
    public function testMergingActionAttribute()
    {
        $route = $this->makeTestRoute();
        $callback = function () {
        };

        $route->mergeAttributes(
            [
            'action' => $callback
            ]
        );

        $this->assertSame($callback, $route->action);
    }

    /**
     * Test if calling prepare method calls required functions
     *
     * @return void
     */
    public function testRoutePreparation()
    {
        $mock = Mockery::mock(Route::class)
            ->makePartial();
            
        $mock->shouldReceive(
            [
                'extractParams' => null,
                'compile' => null
            ]
        )
            ->once();
            
        $mock->prepare();
    }


    /**
     * Construct new route with default values
     *
     * @param array $params
     *
     * @return Route
     */
    protected function makeTestRoute(array $params = [])
    {
        $args = array_values(
            array_merge($this->defaultRouteArgs(), $params)
        );

        return new Route(...$args);
    }

    /**
     * Mock default route args
     *
     * @return array
     */
    protected function defaultRouteArgs()
    {
        return [
            'methods' => ['GET'],
            'uri' => '',
            'action' => []
        ];
    }
}
