<?php

use Ink\Routing\Route;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class RouteTest extends MockeryTestCase
{

    /**
     * Test basic fields in constructor
     *
     * @group dev
     * @return void
     */
    public function testRouteInitialization()
    {
        $methods = ['GET', 'POST'];
        $uri = 'foo';
        $route = new Route(['GET', 'POST'], $uri, 'Controller@action');

        $this->assertEquals($methods, $route->methods);
        $this->assertEquals($uri, $route->uri);
    }

    /**
     * Chech if params are extracted properly
     *
     * @group dev
     * @return void
     */
    public function testRouteParamExtraction()
    {
        $uri = "/foo/{bar}/{baz}";
        $route = new Route(['GET'], $uri, null);

        $route->extractParams();

        $this->assertEquals(['bar', 'baz'], $route->params);
    }

    /**
     * Test whether routes are properly compiled
     *
     * @group dev
     * @return void
     */
    public function testRouteCompilation()
    {
        $uri = "/foo/{bar}/{baz}?query=xxx";
        $exceptation = "/foo/(?P<bar>[a-zA-Z\\d_-]+)/(?P<baz>[a-zA-Z\\d_-]+)?query=xxx";
        $route = new Route([], $uri, null);

        $route->compile();
        
        $this->assertSame($exceptation, $route->wpUri);
    }

    /**
     * Tests if attributes passed as array are merged into route
     *
     * @group dev
     * @return void
     */
    public function testPrefixSetup()
    {
        $uri = "/foo";
        $route = new Route([], $uri, null);

        $route->prefix('bar');
        $this->assertSame('/bar/foo', $route->uri);
        $route->prefix('/baz');
        $this->assertSame('/baz/bar/foo', $route->uri);
    }
    
    /**
     * Tests if attributes passed as array are merged into route
     *
     * @group dev
     * @return void
     */
    public function testAttributeMergeCall()
    {
        $mock = Mockery::mock(Route::class, [['GET'], '/foo', null])
            ->makePartial();

        $mock->shouldReceive('prefix')
            ->with('bar')
            ->once();

        $mock->shouldReceive('module')
            ->with('stamp/v1')
            ->once();
        
        $mock->mergeAttributes([
            'prefix' => 'bar',
            'module' => 'stamp/v1'
        ]);
    }

    /**
     * Undocumented function
     *
     * @group dev
     * @return void
     */
    public function testRoutePreparation()
    {
        $mock = Mockery::mock(Route::class)
            ->makePartial();
            
        $mock->shouldReceive([
                'extractParams' => null,
                'compile' => null
            ])
            ->once();
            
        $mock->prepare();
    }


}