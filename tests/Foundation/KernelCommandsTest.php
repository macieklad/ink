<?php

namespace Tests\Foundation;

use Whoops\Run;
use DI\Container;
use Ink\Foundation\Theme;
use Ink\Config\Repository;
use Ink\Foundation\Kernel;
use Psr\Container\ContainerInterface;
use Whoops\Handler\PrettyPageHandler;
use Ink\Foundation\Bootstrap\HandleErrors;
use Ink\Foundation\Bootstrap\LoadServices;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Ink\Foundation\Bootstrap\LoadConfiguration;

class KernelCommandsTest extends MockeryTestCase
{
    /**
     * Set up the test
     *
     * @return void
     */
    public function setUp(): void
    {
        $this->whoops = \Mockery::mock('overload:' . Run::class);
        $this->whoopsHandler = \Mockery::mock(PrettyPageHandler::class);
        $this->repository = new Repository(
            [
                'theme' => [
                    'devMode' => true
                ]
            ]
        );
    }

    /**
     * Ensure that custom error handling occurs only in
     * development mode
     *
     * @return void
     */
    public function testHandleErrorCommandFiresInDevMode()
    {    
        $command = $this->makeHandleErrorCommand();

        $this->whoops->shouldReceive('pushHandler')
            ->with($this->whoopsHandler)
            ->once();

        $this->whoops->shouldReceive('register')
            ->once();

        $command->fire();
    }

    /**
     * Ensure that custom error handling is not used in
     * production mode
     *
     * @return void
     */
    public function testHandleErrorCommandIsNotFiringInProduction()
    {
        $command = $this->makeHandleErrorCommand();
        $this->repository->set('theme.devMode', false);

        $this->whoops
            ->shouldNotReceive('register');
        
        $command->fire();
    }

    /**
     * Check if command that loads config, injects files into
     * repository and aliases it as config inside the theme
     *
     * @return void
     */
    public function testConfigurationCommandLoadsConfigProperly()
    {
        $theme = new Theme(__DIR__);
        $command = new LoadConfiguration($theme);
        
        $command->fire();

        $this->assertArrayHasKey('foo', $theme['config']->all());
        $this->assertArrayHasKey('bar', $theme['config']->all());
        $this->assertSame('baz', $theme['config']->get('foo.bar'));
        $this->assertSame('bazz', $theme['config']->get('bar.baz'));
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function testLoadServicesCommandCallsEachOfProvidedServices()
    {
        $container = \Mockery::mock(ContainerInterface::class);
        $repository = \Mockery::mock(Repository::class);
        $command = new LoadServices($container);
        $services = [
            'Foo\Bar',
            'Baz'
        ];

        $container->shouldReceive('get')
            ->with('config')
            ->once()
            ->andReturn($repository);

        $repository->shouldReceive('get')
            ->with('theme.providers', [])
            ->once()
            ->andReturn($services);
            

        $container->shouldReceive('call')
            ->withArgs(
                function ($service) use ($services) {
                    return in_array($service[0], $services);
                }
            )
            ->times(count($services));

        $command->fire();
    }

    /**
     * Mock concrete HandleErrors kernel command
     * 
     * @return HandleErrors
     */
    protected function makeHandleErrorCommand()
    {
        return new HandleErrors(
            $this->repository,
            $this->whoops, 
            $this->whoopsHandler
        );
    }

}