<?php

namespace Ink\Container;

use DI\Container;
use DI\ContainerBuilder;

/**
 * @see DI\Container
 */
class ContainerProxy
{
    /**
     * Container definitions
     *
     * @var array
     */
    protected static $definitions = [];

    /**
     * Singleton instance of container
     */
    protected static $instance;

    
    /**
     * Container instance 
     *
     * @var Container
     */
    protected $container;

    /**
     * Instantiate proxy container class
     * 
     * @return void
     */
    public function __construct()
    {
        $this->makeContainer();   
    }
    
    /**
     * Add definition file for PHP-DI container
     *
     * @param mixed $definitionPath
     * @return void
     */
    public static function addDefinitions($definitionPath)
    {
        array_push(static::$definitions, $definitionPath);
    }

    /**
     * Return proxy singleton instance
     *
     * @return void
     */
    public static function getInstance()
    {
        if (is_null(static::$instance)) {
            static::$instance = new static;
        }

        return static::$instance;
    }

    /**
     * Create and initialize container signleton
     *
     * @return void
     */
    protected function makeContainer()
    {
        $builder = new ContainerBuilder();

        foreach (static::$definitions as $definition) {
            $builder->addDefinitions($definition);
        }
        
        $this->container = $builder->build();
    }

    /**
     * Pass non-existent functions to the container behind the proxy
     *
     * @param string $method
     * @param array $params
     * @return void
     */
    public function __call(string $method, array $params) 
    {
        return $this->container->$method(...$params);
    }
}


