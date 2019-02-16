<?php

namespace Ink\Contracts\Hooks;

interface ActionManager
{
    /**
     * Construct the manager for a named action
     *
     * @param string $name
     */
    public function __construct(string $name);

    /**
     * Manage action with given name
     *
     * @param string $name
     * @return ActionManager
     */
    public function name(string $name): ActionManager;

    /**
     * Add handlers to the action type, intentified by 
     * priority, and accepting max ammount of args.
     * 
     * @param string|Closure|array $with
     * @param int $priority
     * @param int $acceptedArgs
     * @return ActionManager 
     */
    public function respond($with, int $priority = 10, int $acceptedArgs = 1): ActionManager;

    /**
     * Dispatch current action with arguments
     *
     * @param mixed ...$args
     * @return void
     */
    public function dispatch(...$args): ActionManager;

    /**
     * Check if action with given name is defined with 
     * any listeners.
     *
     * @return boolean
     */
    public function exists(): bool;

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
     * @param integer $priority
     * @return ActionManager
     */
    public function detach($handlers, int $priority = 10): ActionManager;

    /**
     * Remove all possible handlers from action of given name
     * and priority.
     *
     * @param integer $priority
     * @return ActionManager
     */
    public function flush(int $priority = 10): ActionManager;
}