<?php

namespace Ink\Foundation;

use ArrayAccess;
use DI\Container;
use function DI\get;
use Ink\Routing\Router;
use DI\ContainerBuilder;
use Ink\Config\Repository;
use Ink\Foundation\Kernel;
use Psr\Container\ContainerInterface;
use Ink\Foundation\Bootstrap\HandleErrors;
use Ink\Foundation\Bootstrap\LoadServices;
use Ink\Foundation\Bootstrap\LoadConfiguration;
use Ink\Contracts\Foundation\Theme as ThemeContract;
use Ink\Contracts\Config\Repository as RepositoryContract;

class Theme implements ThemeContract
{

    /**
     * Container instance
     *
     * @var DI\Container
     */
    protected $container;

    /**
     * Base path of the theme directory
     * 
     * @var string
     */
    protected $basePath = '';

    /**
     * Create a new theme class with root directory
     * 
     * @param string $basePath
     */
    public function __construct(string $basePath = null)
    {   
        $this->prepareContainer();
        
        if ($basePath) {
            $this->setBasePaths($basePath);
        }
        
        $this->createBaseAliases();
    }

    /**
     * Get the container for the application
     *
     * @return void
     */
    protected function prepareContainer()
    {
        $builder = new ContainerBuilder;

        $builder->addDefinitions([
            RepositoryContract::class => get(Repository::class)
        ]);

        $this->container = $builder->build();
    }

    /**
     * Theme container accessor
     *
     * @return void
     */
    public function container(): ContainerInterface
    {
        return $this->container;
    }

    /**
     * Add basic aliases needed for the theme 
     *
     * @return void
     */
    public function createBaseAliases()
    {
        $container = $this->container();

        $container->set('theme', $this);
        $container->set(Theme::class, $this);
        $container->set(ThemeContract::class, $this);
        $container->set('container', $container);
        $container->set(Container::class, $container);
        $container->set(ContainerInterface::class, $container);
        $container->set('kernel', $container->get(Kernel::class));
        $container->set('router', $container->get(Router::class));
        $container->set('config', $container->get(RepositoryContract::class));
    }

    /**
     * Register application base paths
     *
     * @param string $path
     * @return void
     */
    protected function setBasePaths(string $path): void
    {
        $container = $this->container();

        $this->basePath = rtrim($path, '\/');

        $container->set('path.base', $this->basePath());
        $container->set('path.config', $this->configPath());
    }


    /**
     * Return path from application root to the pointed path
     *
     * @param string $path
     * @return void
     */
    public function basePath(string $path = ''): string
    {
        return $this->basePath . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }

    /**
     * Return path to the path inside config directory
     *
     * @param string $path
     * @return void
     */
    public function configPath(string $path = ''): string
    {
        return $this->basePath . DIRECTORY_SEPARATOR . "config" . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }


    /**
     * Bootstrap the theme core components
     *
     * @return void
     */
    public function bootstrap(): void
    {
        $this['kernel']->executeCommands([
            LoadConfiguration::class,
            HandleErrors::class,
            LoadServices::class
        ]);
    }

    /**
     * Check if item exists inside container
     *
     * @param integer|string $offset
     * @return void
     */
    public function offsetExists($offset): bool
    {
        return $this->container->has($offset);
    }

    /**
     * Get item by its key
     *
     * @param integer|string $offset
     * @return void
     */
    public function offsetGet($offset) 
    {
        return $this->container->get($offset);
    }

    /**
     * Set item at given key inside container
     *
     * @param string $offset
     * @param mixed $value
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->container->set($offset, $value);
    }

    /**
     * Set item to null inside container
     *
     * @param string $offset
     * @return void
     */
    public function offsetUnset($offset)
    {
        $this->container->set($offset, null);
    }
}