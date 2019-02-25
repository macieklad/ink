<?php

namespace Ink\Contracts\Hooks;

use Psr\Container\ContainerInterface;

interface ActionManager
{
    /**
     * Construct the manager for an action
     *
     * @param string $container
     */
    public function __construct(ContainerInterface $container);

    /**
     * Manage action with given name
     *
     * @param string $action
     * 
     * @return Ink\Contracts\Hooks\ActionManager
     */
    public function name(string $action): ActionManager;

    /**
     * Add handlers to the action type, intentified by 
     * priority, and accepting max ammount of args.
     * 
     * @param string|Closure|array $with
     * @param int                  $priority
     * @param int                  $acceptedArgs
     * 
     * @return mixed
     */
    public function handle(
        $with,
        int $priority = 10,
        int $acceptedArgs = 1
    );

    /**
     * Dispatch current action with arguments
     *
     * @param mixed ...$args
     * 
     * @return void
     */
    public function dispatch(...$args): ActionManager;

    /**
     * Check if action with given name is defined with 
     * any listeners.
     *
     * @param mixed $handler
     * 
     * @return boolean
     */
    public function exists($handler): bool;

    /**
     * Inform how many times action was already called
     *
     * @return integer
     */
    public function count(): int;

    /**
     * Detach handler or multiple handlers from action,
     * with given priority; 
     *
     * @param Closure|string|array $handlers
     * @param integer              $priority
     * 
     * @return Ink\Contracts\Hooks\ActionManager
     */
    public function detach($handlers, int $priority = 10): ActionManager;

    /**
     * Remove all possible handlers from action of given name
     * and priority.
     *
     * @param integer $priority
     * 
     * @return Ink\Contracts\Hooks\ActionManager
     */
    public function flush(int $priority = 10): ActionManager;

    /**
     * Set the namespace of handlers, from which they can
     * be inferred without providing namespace first.
     *
     * @param string $namespace
     * 
     * @return void
     */
    public function setHandlerNamespace(string $namespace) : ActionManager;

    /**
     * Force compilation of callable handlers passed
     * as arguments to action
     *
     * @return Ink\Contracts\Hooks\ActionManager
     */
    public function forceCompilation() : ActionManager;
}