<?php

namespace Ink\Contracts\Hooks;

interface FilterManager
{
    /**
     * Construct the manager for a named filter
     *
     * @param string $name
     */
    public function __construct(string $name);

    /**
     * Manage filter with the given name
     *
     * @param  string $name
     * @return FilterManager
     */
    public function name(string $name) : FilterManager;

    /**
     * Apply the filter to the provided value and pass
     * some arguments to it.
     *
     * @param  mixed $value
     * @param  mixed ...$args
     * @return void
     */
    public function apply($value, ...$args);

    /**
     * Add a handler to the filter, with priority and 
     * maximum allowed arguments.
     *
     * @param  Closure|string|array $handler
     * @param  integer              $priority
     * @param  integer              $acceptedArgs
     * @return FilterManager
     */
    public function add($handler, int $priority = 10, int $acceptedArgs = 1) : FilterManager;

    /**
     * Check if filter with the given name is defined
     * with any handlers.
     *
     * @return boolean
     */
    public function exists() : bool;


    /**
     * Detach handler or multiple handlers from filter,
     * with given priority.
     *
     * @param  Closure|string|array $handlers
     * @param  integer              $priority
     * @return FilterManager
     */
    public function detach($handlers, int $priority = 10) : FilterManager;

    /**
     * Remove all possible handlers of given priority
     * from the filter.
     *
     * @param  integer $priority
     * @return FilterManager
     */
    public function flush(int $priority = 10) : FilterManager;
}