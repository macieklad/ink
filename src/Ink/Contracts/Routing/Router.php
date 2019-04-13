<?php

namespace Ink\Contracts\Routing;

interface Router
{
    /**
     * Register get request
     *
     * @param string $uri
     * @param mixed  $attributes
     * 
     * @return void
     */
    public function get(string $uri, $attributes): void;

    /**
     * Register post request
     *
     * @param string $uri
     * @param mixed  $attributes
     * 
     * @return void
     */
    public function post(string $uri, $attributes): void;

    /**
     * Register put request
     *
     * @param string $uri
     * @param mixed  $attributes
     * 
     * @return void
     */
    public function put(string $uri, $attributes): void;

    /**
     * Register delete request
     *
     * @param string $uri
     * @param mixed  $attributes
     * 
     * @return void
     */
    public function delete(string $uri, $attributes): void;

    /**
     * Load routes from file or closure and add some attributes to them
     *
     * @param mixed $routes
     * @param array $attributes
     * 
     * @return void
     */
    public function loadRoutes($routes, array $attributes = []): void;

    /**
     * Add registered routes to the wordpress api 
     *
     * @return void
     */
    public function listen(): void;

    /**
     * Set the controller namespace from which router should resolve 
     * string actions.
     *
     * @param string $namespace
     * 
     * @return void
     */
    public function setControllerNamespace(string $namespace) : void;
}
