<?php

namespace Ink\Routing;

use Closure;
use Ink\Routing\Route;

class Router 
{
    /**
     * Possible http methods for the request
     *
     * @var array
     */
    protected $verbs = ['GET', 'POST'];


    /**
     * Array of attribute arrays to merge onto routes
     *
     * @var array
     */
    protected $attributeStack = [];

    /**
     * Register GET request route
     *
     * @param string $uri
     * @param mixed $action
     * @return void
     */
    public function get(string $uri, $attributes)
    {
        $route = $this->createRoute(['GET'], $uri, $attributes);
    }

    /**
     * Create new route and add it to the register
     *
     * @param array $methods
     * @param string $uri
     * @param mixed $attributes
     * @return void
     */
    public function createRoute(array $methods, string $uri, $attributes)
    {  
        if (is_string($attributes)) {
            $attributes = ['action' => $attributes];
        }

        $route = new Route($methods, $uri, $attributes['action']);
        $route->mergeAttributes($attributes);

        $this->addRoute($route);
    }

    /**
     * Load routes into router, they can be files
     * or single route object
     *
     * @param mixed $routes
     * @param array $attributes
     * @return void
     */
    public function loadRoutes($routes, array $attributes = []) 
    {
        $this->updateAttributeStack($attributes);

        if ($routes instanceof Closure) {
            $routes($this);
        } else {
            require $routes;
        }

        array_pop($this->attributeStack);
    }

    /**
     * Add attributes to the stack
     *
     * @param array $attributes
     * @return void
     */
    public function updateAttributeStack(array $attributes)
    {
        array_push($this->attributeStack, $attributes);
    }

    /**
     * Prepare the route and register it
     *
     * @param Route $route
     * @return void
     */
    public function addRoute(Route $route)
    {
        foreach ($this->attributeStack as $attributeGroup) {
            $route->mergeAttributes($attributeGroup);
        }

        $route->prepare();
        $this->registerRoute($route);
    }

    /**
     * Register WP_API route
     *  
     * @param \Stamp\Http\Route $route
     * @return void
     */
    public function registerRoute(Route $route)
    {
        add_action('rest_api_init', function () use ($route) {
            register_rest_route('v1', $route->wpUri, array(
                'methods' => $route->methods,
                'callback' => function ($data) {
                    return 'Hello';
                },
            ));
        });   
    }

    /**
     * Call the registrar method if it is not present on router
     *
     * @param string $name
     * @param array $arguments
     * @return void
     */
    public function __call(string $name, array $arguments)
    {
        return (new RouteRegistrar($this))->$name(...$arguments);
    }
}