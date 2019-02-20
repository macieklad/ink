<?php

namespace Ink\Aliases;

use Closure;
use RuntimeException;

abstract class Alias
{
    /**
     * The container instance being for holding aliases.
     *
     * @var \Psr\Container\ContainerInterface
     */
    protected static $container;

    /**
     * The resolved object instances.
     *
     * @var array
     */
    protected static $resolvedInstance;
    
    /**
     * Get the mockable class for the bound instance.
     *
     * @return string|null
     */
    public static function getMockableClass()
    {
        $root = static::getAliasRoot();
        
        return $root ? get_class($root) : null;
    }

    /**
     * Get the root object behind the alias.
     *
     * @return mixed
     */
    public static function getAliasRoot()
    {
        return static::resolveAliasedInstance(static::getAliasAccessor());
    }

    /**
     * Get the registered name of the component.
     *
     * @throws \RuntimeException
     *
     * @return string
     */
    protected static function getAliasAccessor()
    {
        throw new RuntimeException(
            'Alias does not implement getAliasAccessor method.'
        );
    }

    /**
     * Resolve the alias root instance from the container.
     *
     * @param string|object $name
     * 
     * @return mixed
     */
    protected static function resolveAliasedInstance($name)
    {
        if (is_object($name)) {
            return $name;
        }

        if (isset(static::$resolvedInstance[$name])) {
            return static::$resolvedInstance[$name];
        }

        return static::$resolvedInstance[$name] = static::$container->get($name);
    }

    /**
     * Clear all of the resolved instances.
     *
     * @return void
     */
    public static function clearResolvedInstances()
    {
        static::$resolvedInstance = [];
    }

    /**
     * Get the theme instance behind the alias.
     *
     * @return \Psr\Container\ContainerInterface
     */
    public static function getAliasContainer()
    {
        return static::$container;
    }

    /**
     * Set the alias container.
     *
     * @param \Psr\Container\ContainerInterface $container
     * 
     * @return void
     */
    public static function setAliasContainer($container)
    {
        static::$container = $container;
    }

    /**
     * Handle dynamic, static calls to the object.
     *
     * @param string $method
     * @param array  $args
     * 
     * @return mixed
     *
     * @throws \RuntimeException
     */
    public static function __callStatic($method, $args)
    {
        $instance = static::getAliasRoot();

        if (!$instance) {
            throw new RuntimeException('An alias root has not been set.');
        }

        return $instance->$method(...$args);
    }
}
