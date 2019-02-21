<?php

namespace Ink\Routing;

use Closure;
use Ink\Routing\Route;
use Psr\Container\ContainerInterface;
use Ink\Contracts\Routing\Router as RouterContract;

class Router implements RouterContract
{
    /**
     * Array of attribute arrays to merge onto routes
     *
     * @var array
     */
    protected $attributeStack = [];

    /**
     * Route array
     * 
     * @var array
     */
    protected $routes = [];

    /**
     * Default controller namespace, from where they are
     * resolved when passed as action string
     * 
     * @var string
     */
    protected $controllerNamespace = '';

    /**
     * Container which will call router actions and prepare them
     *
     * @var DI\Container
     */
    protected $container;

    /**
     * Construct new router object
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Register GET request route
     *
     * @param string $uri
     * @param mixed  $attributes
     * 
     * @return void
     */
    public function get(string $uri, $attributes): void
    {
        $route = $this->createRoute(['GET'], $uri, $attributes);
    }

    /**
     * Register POST request route
     *
     * @param string $uri
     * @param mixed  $attributes
     * 
     * @return void
     */
    public function post(string $uri, $attributes): void
    {
        $route = $this->createRoute(['POST'], $uri, $attributes);
    }

    /**
     * Register PATCH request route
     *
     * @param string $uri
     * @param mixed  $attributes
     * 
     * @return void
     */
    public function put(string $uri, $attributes): void
    {
        $route = $this->createRoute(['PUT'], $uri, $attributes);
    }

    /**
     * Register DELETE request route
     *
     * @param string $uri
     * @param mixed  $attributes
     * 
     * @return void
     */
    public function delete(string $uri, $attributes): void
    {
        $route = $this->createRoute(['DELETE'], $uri, $attributes);
    }
    
    /**
     * Create new route and add it to the register
     *
     * @param array  $methods
     * @param string $uri
     * @param mixed  $attributes
     * 
     * @return void
     */
    public function createRoute(array $methods, string $uri, $attributes): void
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
     * 
     * @return void
     */
    public function loadRoutes($routes, array $attributes = []): void
    {
        $this->updateAttributeStack($attributes);

        if ($routes instanceof Closure) {
            $routes($this);
        } else {
            $router = $this;

            include $routes;
        }

        array_pop($this->attributeStack);
    }

    /**
     * Add attributes to the stack
     *
     * @param array $attributes
     * 
     * @return void
     */
    public function updateAttributeStack(array $attributes): void
    {
        array_push($this->attributeStack, $attributes);
    }

    /**
     * Prepare the route and register it
     *
     * @param Route $route
     * 
     * @return void
     */
    public function addRoute(Route $route): void
    {
        foreach ($this->attributeStack as $attributeGroup) {
            $route->mergeAttributes($attributeGroup);
        }

        $route->prepare();
        array_push($this->routes, $route);
    }

    /**
     * Hook router into wordpress
     *
     * @return void
     */
    public function listen(): void
    {
        add_action(
            'rest_api_init', 
            [
                $this,
                'addWordpressRoutes'
            ]
        );           
    }

    /**
     * Add registered routes to wordpress
     *
     * @return void
     */
    public function addWordpressRoutes()
    {
        foreach ($this->routes() as $route) {
            register_rest_route(
                $route->module, $route->wpUri, [
                'methods' => $route->methods,
                'callback' => $this->compileAction($route)
                ]
            );
        }
    }

    /**
     * Compile action used in route into a callback
     * which can be executed by wordpress
     *
     * @param Route $route
     * 
     * @throws InvalidArgumentException
     * 
     * @return Closure
     */
    public function compileAction(Route $route): Closure
    {
        if (is_string($route->action)) {
            return $this->compileStringAction($route);
        }

        if ($route->action instanceof Closure) {
            return $this->compileCallbackAction($route);
        }

        throw new \InvalidArgumentException(
            "Route {$route->uri} action could not be compiled,
             as it is not string or callback, please fix it"
        );
    }

    /**
     * Compile action passed as string into a callback
     *
     * @param Route $route
     * 
     * @throws InvalidArgumentException
     * 
     * @return void
     */
    protected function compileStringAction(Route $route): Closure 
    {
        $actionParts = explode('@', $route->action);


        if (count($actionParts) < 2) {
            throw new \InvalidArgumentException(
                "Action {$route->action} for route {$route->uri} isn't valid,
                 we couldn't extract controller and method parts from it.
                 Ensure it is in Controller@action format"
            );
        }

        $controller = $this->controllerNamespace . '\\' . $actionParts[0];

        if (! \class_exists($controller)) {
            throw new \InvalidArgumentException(
                "Class {$controller} handling the {$route->uri} route doesn't exist !
                 Specify a valid one, maybe it's a typo"
            );
        }

        $method = $actionParts[1];
        
        return function ($req = null) use ($controller, $method) {
            return $this->container->call(
                [$controller, $method], [
                'req' => $req
                ]
            );
        };
    }

    /**
     * Compile action passed as callback
     *
     * @param Route $route
     * 
     * @return Closure
     */
    protected function compileCallbackAction(Route $route): Closure
    {
        return function ($req = null) use ($route) {
            return $this->container->call(
                $route->action, [
                'req' => $req
                ]
            );
        };
    }

    /**
     * Set the base namespace, from where controllers will be loaded
     * 
     * @param string $namespace
     * 
     * @return void
     */
    public function setControllerNamespace(string $namespace): void
    {
        $this->controllerNamespace = $namespace;
    }

    /**
     * Return registered routes
     *
     * @return void
     */
    public function routes(): array
    {
        return $this->routes;
    }

    /**
     * Call the registrar method if it is not present on router
     *
     * @param string $name
     * @param array  $arguments
     * 
     * @return void
     */
    public function __call(string $name, array $arguments)
    {
        return (new RouteRegistrar($this))->$name(...$arguments);
    }
}