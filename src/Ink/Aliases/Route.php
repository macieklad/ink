<?php

namespace Ink\Aliases;

/**
 * Router alias access
 *
 * @see    \Ink\Routing\Route
 * @see    \Ink\Routing\Router
 * @see    \Ink\Routing\RouteRegistrar
 * @method static void get(string $uri, mixed $handler)
 * @method static void post(string $url, mixed $handler)
 * @method static void put(string $url, mixed $handler)
 * @method static void delete(string $url, mixed $handler)
 * @method static void prefix(string $with)
 * @method static void module(string $name)
 * @method static void group(\Closure $routes)
 */
class Route extends Alias
{
    /**
     * Return alias underlying container entry
     *
     * @return string
     */
    public static function getAliasAccessor() 
    {
        return 'router';
    }
}
