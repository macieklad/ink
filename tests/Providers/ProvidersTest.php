<?php

namespace Tests\Providers;

use Ink\Aliases\Alias;
use Ink\Routing\Router;
use Ink\Providers\AliasProvider;
use Ink\Providers\RoutingProvider;
use Ink\Contracts\Foundation\Theme;
use Ink\Contracts\Config\Repository;
use Psr\Container\ContainerInterface;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class ProvidersTest extends MockeryTestCase
{
    /**
     * Set up the theme for each test
     *
     * @return void
     */
    public function setUp(): void
    {
        $this->theme = \Mockery::mock(Theme::class);
    }

    /**
     * Assure routing provider behaves correctly
     *
     * @return void
     */
    public function testRoutingProviderRegistersRoutesProperly()
    {
        $routesRelDir = 'src/Api/routes.php';
        $controllersNamespace = 'Theme\Api\Controllers';
        $router = \Mockery::mock(Router::class);
        $repository = \Mockery::mock(Repository::class);
        $provider = new RoutingProvider($this->theme);

        $this->theme->shouldReceive('basePath')
            ->with($routesRelDir)
            ->once()
            ->andReturn($routesRelDir);

        $repository->shouldReceive('get')
            ->with('routing.routes', $routesRelDir)
            ->andReturn($routesRelDir)
            ->once();

        $repository->shouldReceive('get')
            ->with('routing.controllerNamespace', $controllersNamespace)
            ->andReturn($controllersNamespace)
            ->once();

        $router->shouldReceive('loadRoutes')
            ->with($routesRelDir)
            ->once();

        $router->shouldReceive('setControllerNamespace')
            ->with($controllersNamespace)
            ->once();

        $router->shouldReceive('listen')
            ->once();

        $provider->boot($router, $repository);
    }

    /**
     * Assure aliases are registered by the provider
     *
     * @return void
     */
    public function testAliasProvidersAddsAliasesProperly()
    {
        $aliases = [
            'Foo' => StubAlias::class,
            'Bar' => StubAlias::class
        ];

        $mock = \Mockery::mock();
        $repository = \Mockery::mock(Repository::class);
        $container = \Mockery::mock(ContainerInterface::class);
        $provider = new AliasProvider($this->theme);

        $repository->shouldReceive('get')
            ->with('aliases', [])
            ->andReturn($aliases);

        $mock->shouldReceive('foo')
            ->twice();

        $container->shouldReceive('get')
            ->with('stub')
            ->andReturn($mock);

        $provider->boot($container, $repository);

        \Foo::foo();
        \Bar::foo();
    }
}

class StubAlias extends Alias {
    public static function getAliasAccessor()
    {
        return 'stub';
    }
}