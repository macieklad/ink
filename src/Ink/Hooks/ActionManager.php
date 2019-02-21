<?php

namespace Ink\Hooks;

use Psr\Container\ContainerInterface;
use Ink\Contracts\Hooks\ActionManager as ActionManagerContract;

class ActionManager implements ActionManagerContract
{
    /**
     * Managed action name
     *
     * @var string
     */
    protected $action = '';

    /**
     * Container instance for calling handlers
     *
     * @var Psr\Container\ContainerInterface;
     */
    protected $container;

    /**
     * Decide whether callable handlers passed to actions
     *  should be compiled before being called
     *
     * @var boolean
     */
    protected $forceCompilation = false;

    /**
     * Namespace from where controllers can be
     * inferred without providing namespace 
     *
     * @var string
     */
    protected $controllerNamespace = '';

    /**
     * Construct the manager for an action
     *
     * @param string $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Manage action with given name
     *
     * @param string $action
     * 
     * @return ActionManager
     */
    public function name(string $action) : ActionManagerContract
    {
        $this->action = $action;

        return $this;
    }

    /**
     * Add handlers to the action type, intentified by 
     * priority, and accepting max ammount of args.
     * 
     * @param string|Closure|array $with
     * @param int                  $priority
     * @param int                  $acceptedArgs
     * 
     * @return ActionManager 
     */
    public function respond(
        $with,
        int $priority = 10,
        int $acceptedArgs = 1
    ) : ActionManagerContract {

        $handler = $this->compileActionHandler($with);
       
        add_action($this->action, $handler, $priority, $acceptedArgs);

        return $this;
    }

    /**
     * Compile handler passed with the action type
     *
     * @param mixed $handler
     * 
     * @return void
     */
    public function compileActionHandler($handler)
    {
        $handlerIsFunction = (is_string($handler) && \function_exists($handler));

        if (\is_callable($handler) || $handlerIsFunction) {
            return $this->forceCompilation 
                ? $this->compileCallableHandler($handler) : $handler;
        }

        if (\is_array($handler)) {
            return $this->compileArrayHandler($handler);
        }

        if (\is_string($handler)) {
            return $this->compileControllerHandler($handler);
        }

        throw \InvalidArgumentException(
            "Your action handler type is not supported by action manager.
             Please change it, maybe it was a typo ? Passed " . gettype($handler)
        );
    }

    /**
     * Compile handler in form of steps defined
     * inside an array
     *
     * @param array $handlers
     * 
     * @return void
     */
    public function compileArrayHandler(array $handlers)
    {
        return function () use ($handlers) {
            $args = \func_get_args();

            foreach ($handlers as $handler) {
                $this->compileActionHandler($handler)(...$args);
            }
        };
    }

    /**
     * Compile handler in form of Controller@handler
     *
     * @param string $handler
     * 
     * @return void
     */
    public function compileControllerHandler(string $handler)
    {
        $parts = explode('@', $handler);
        $controller = $parts[0];
        $handler = $parts[1];

        if (!\class_exists($controller)) {
            $controller = $this->controllerNamespace . '\\' . $controller;
        }

        if (!\class_exists($controller)) {
            throw new \InvalidArgumentException(
                "Controller {$controller} doesn't exist, please specify 
                 a callable controller, or set manager namespace correctly first."
            );
        }

        if (!\is_callable([$controller, $handler])) {
            throw new \InvalidArgumentException(
                "Controller {$controller} method {$handler} is not callable,
                 it may be a typo, or the method does not exist."
            );
        }

        return function () use ($controller, $handler) {
            $args = \func_get_args();

            return $this->container->call(
                [$controller, $handler],
                array_merge($args, ['manager' => $this])
            );
        };
    }

    /**
     * Convert action hook into callback
     * by passing it through container
     *
     * @param callable $handler
     * 
     * @return void
     */
    public function compileCallableHandler($handler)
    {
        return function () use ($handler) { 
            $args = \func_get_args();

            return $this->container->call(
                $handler,
                array_merge($args, ['manager' => $this])
            );
        };
    }

    /**
     * Dispatch current action with arguments
     *
     * @param mixed ...$args
     * 
     * @return void
     */
    public function dispatch(...$args) : ActionManagerContract
    {
        do_action($this->action, ...\func_get_args());

        return $this;
    }

    /**
     * Check if action with given name is defined with 
     * any listeners.
     *
     * @param mixed $handler
     * 
     * @return boolean
     */
    public function exists($handler) : bool
    {
        return has_action($this->action, $handler);
    }

    /**
     * Inform how many times action was already called
     *
     * @return integer
     */
    public function count() : int
    {
        return did_action($this->action);
    }

    /**
     * Detach handler or multiple handlers from action,
     * with given priority; 
     *
     * @param Closure|string|array $handler
     * @param integer              $priority
     * 
     * @return Ink\Contracts\Hooks\ActionManager
     */
    public function detach($handler, int $priority = 10) : ActionManagerContract
    {
        remove_action($this->action, $handler, $priority);

        return $this;
    }

    /**
     * Remove all possible handlers from action of given name
     * and priority.
     *
     * @param integer $priority
     * 
     * @return Ink\Contracts\Hooks\ActionManager
     */
    public function flush(int $priority = 10) : ActionManagerContract
    {
        remove_all_actions($this->action, $priority);

        return $this;
    }

    /**
     * Set the controller namespace, from which they can
     * be inferred without providing namespace first.
     *
     * @param string $namespace
     * 
     * @return void
     */
    public function setControllerNamespace(string $namespace) : ActionManagerContract
    {
        $this->controllerNamespace = $namespace;

        return $this;
    }

    /**
     * Force compilation of callable handlers passed
     * as arguments to action
     *
     * @return Ink\Contracts\Hooks\ActionManager
     */
    public function forceCompilation() : ActionManagerContract
    {
        $this->forceCompilation = true;

        return $this;
    }
}