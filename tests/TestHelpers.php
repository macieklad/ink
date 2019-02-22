<?php

namespace Tests;

use Mockery\MockInterface;

class TestHelpers
{
    /**
     * Proxy object to mock global functions
     *
     * @var Mockery\MockInterface
     */
    public static $functions;

    /**
     * Get protected or private property from an object
     *
     * @param mixed  $object
     * @param string $propertyName
     * 
     * @return mixed
     */
    public static function getProperty($object, $propertyName)
    {
        $reflection = new \ReflectionClass($object);
        $property = $reflection->getProperty($propertyName);
        $property->setAccessible(true);
        return $property->getValue($object);
    }

    /**
     * Call function on global functions mock
     *
     * @param string $func
     * @param array $args
     * 
     * @return void
     */
    public static function passGlobalCall(string $func, array $args)
    {
        if (!(static::$functions instanceof MockInterface)) {
            throw new \RuntimeException(
                'Global $functions object was not instantiated, 
                 use MocksGlobals trait and call clear Globals method first'
            );
        }

        return static::$functions->{$func}(...$args);
    }

    /**
     * Return global functions mock
     *
     * @return void
     */
    public static function functions()
    {
        return static::$functions;
    }
}