<?php

namespace Ink\Routing;

use InvalidArgumentException;

class RouteRegistrar
{
    /**
     * Theme router instance
     *
     * @var Router
     */
    protected $router;

    /**
     * Methods which should be delegated to router while called
     * on registar instance
     *
     * @var array
     */
    protected $passthru = ['get', 'post', 'put', 'delete'];

    /**
     * Allowed prefixes to set
     *
     * @var array
     */
    protected $allowedAttributes = ['prefix', 'module'];

    /**
     * Attributes passed to routes registered by registrar
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * Create new registrar object
     *
     * @param Router $router
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    /**
     * Register route on the router instance
     *
     * @param string $method
     * @param string $uri
     * @param mixed  $action
     *
     * @return void
     */
    public function registerRoute(string $method, string $uri, $action)
    {
        $this->router->{$method}(
            $uri,
            array_merge($this->attributes, ['action' => $action])
        );   
    }

    /**
     * Group multiple routes with the same attributes
     *
     * @param mixed $routes
     * 
     * @return void
     */
    public function group($routes) 
    {
        $this->router->loadRoutes($routes, $this->attributes);
    }

    /**
     * Set the route or group attribute
     *
     * @param string $name
     * @param string $value
     *
     * @return RouteRegistrar
     */
    public function attribute(string $name, string $value)
    {
        $this->attributes[$name] = $value;

        return $this;
    }

    /**
     * Dynamically call methods that set new route attributes
     *
     * @param string $method
     * @param array  $parameters
     * 
     * @return mixed
     */
    public function __call(string $method, array $parameters)
    {
        if (in_array($method, $this->passthru)) {
            return $this->registerRoute($method, ...$parameters);
        }

        if (in_array($method, $this->allowedAttributes)) {
            return $this->attribute($method, $parameters[0]);
        } 

        throw new InvalidArgumentException(
            "Method {$method} is not allowed on registrar method,
             maybe you mistyped them or they are not allowed by registrar ?"
        );
    }
}
