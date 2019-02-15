<?php

namespace Ink\Foundation;

use ArrayAccess;
use DI\Container;
use function DI\create;
use Ink\Routing\Router;
use DI\ContainerBuilder;
use Ink\Config\Repository;
use Ink\Foundation\Kernel;
use Ink\Foundation\Bootstrap\HandleErrors;
use Ink\Foundation\Bootstrap\LoadServices;
use Ink\Foundation\Bootstrap\LoadConfiguration;


class Theme implements ArrayAccess
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
            'router' => create(Router::class),
            'config' => create(Repository::class)
        ]);

        $this->container = $builder->build();
    }

    /**
     * Theme container Accessor
     *
     * @return void
     */
    public function container()
    {
        return $this->container;
    }

    /**
     * Bootstrap the theme core components
     *
     * @return void
     */
    public function bootstrap() 
    {
        $this['kernel']->executeCommands([
            LoadConfiguration::class,
            HandleErrors::class,
            LoadServices::class
        ]);
    }

    /**
     * Add basic aliases useful for the theme 
     *
     * @return void
     */
    public function createBaseAliases()
    {
        $container = $this->container();

        $container->set('theme', $this);
        $container->set(Theme::class, $this);
        $container->set('container', $container);
        $container->set(Container::class, $container);
        $container->set('kernel', $container->get(Kernel::class));
    }

    /**
     * Register application base paths
     *
     * @param string $path
     * @return void
     */
    protected function setBasePaths(string $path)
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
    public function basePath(string $path = '')
    {
        return $this->basePath . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }

    /**
     * Return path to the path inside config directory
     *
     * @param string $path
     * @return void
     */
    public function configPath(string $path = '')
    {
        return $this->basePath . DIRECTORY_SEPARATOR . "config" . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }

    public function offsetExists($offset)
    {
        return $this->container->has($offset);
    }

    public function offsetGet($offset)
    {
        return $this->container->get($offset);
    }

    public function offsetSet($offset, $value)
    {
        
        $this->container->set($offset, $value);
    }

    public function offsetUnset($offset)
    {
        $this->container->set($offset, null);
    }
}