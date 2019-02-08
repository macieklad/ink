<?php

namespace Ink\Foundation;

use DI\Container;
use DI\ContainerBuilder;
use Ink\Foundation\Kernel;

class Theme
{
    /**
     * Container Instance
     *
     * @var DI\Container
     */
    protected static $container;

    /**
     * Theme kernel
     *
     * @var Stamp\Kernel
     */
    protected $kernel;

    /**
     * Create a new theme class
     * 
     */
    public function __construct()
    {
        $this->createBaseAliases();
        $this->loadKernel();
        $this->startServices();
    }

    /**
     * Create kernel class and assign it to the theme
     *
     * @return void
     */
    public function loadKernel() {
        $this->kernel = static::getContainer()->get(Kernel::class);
    }
    
    /**
     * Load needed services for the theme
     *
     * @return void
     */
    public function startServices() {
        $this->kernel->loadServices();
    }

    /**
     * Add basic aliases useful for the theme 
     *
     * @return void
     */
    public function createBaseAliases()
    {
        $container = static::getContainer();

        $this->instance('theme', $this);
        $this->instance(Theme::class, $this);
        $this->instance('container', $container);
        $this->instance(Container::class, $container);
    }

    /**
     * Return a class from theme container
     *
     * @param string $instance
     * @return void
     */
    public function get(string $instance)
    {
        return static::getContainer()->get($instance);
    }

    /**
     * Register an entry in container
     *
     * @param string $namespace
     * @param string $instance
     * @return void
     */
    public function instance(string $namespace, $instance)
    {
        static::getContainer()->set($namespace, $instance);
    }

    /**
     * Invoke container object and call a function on it
     *
     * @param [type] $callable
     * @param array $params
     * @return void
     */
    public function call($callable, array $params = [])
    {
        static::getContainer()->call($callable, $params);
    }

    /**
     * Get container singleton for the theme
     *
     * @return DI\Container
     */
    public static function getContainer() 
    {
        if (is_null(static::$container)) {
            static::makeContainer();
        }

        return static::$container;
    }

    /**
     * Create and initialize container signleton
     *
     * @return void
     */
    protected static function makeContainer() {
        $builder = new ContainerBuilder();
        $builder->addDefinitions(join_paths(__DIR__, "../config/theme.php"));

        static::$container = $builder->build();
    }

}