<?php

namespace Ink\Contracts\Hooks;

use Psr\Container\ContainerInterface;

interface FilterManager
{
    /**
     * Construct the manager for a named filter
     *
     * @param Psr\Container\ContainerInterface $container
     */
    public function __construct(ContainerInterface $container);

    /**
     * Manage filter with the given name
     *
     * @param string $filter
     * 
     * @return Ink\Contracts\Hooks\FilterManager
     */
    public function name(string $filter) : FilterManager;

    /**
     * Apply the filter to the provided value and pass
     * some arguments to it.
     *
     * @param mixed $value
     * @param mixed ...$args
     * 
     * @return void
     */
    public function apply($value, ...$args);

    /**
     * Use mutating function while calling filter,
     * and define its priority and allowed args.
     *
     * @param Closure|string|array $mutator
     * @param integer              $priority
     * @param integer              $acceptedArgs
     * 
     * @return mixed
     */
    public function use(
        $mutator,
        int $priority = 10,
        int $acceptedArgs = 1
    );

    /**
     * Check if filter with the given name is defined
     * with any mutators.
     *
     * @param mixed $mutator
     * 
     * @return boolean
     */
    public function exists($mutator) : bool;

    /**
     * Detach single or multiple mutators 
     * from the filter, with given priority.
     *
     * @param Closure|string|array $mutators
     * @param integer              $priority
     * 
     * @return Ink\Contracts\Hooks\FilterManager
     */
    public function detach($mutators, int $priority = 10) : FilterManager;

    /**
     * Remove all possible mutators of given
     * priorityfrom the filter.
     *
     * @param integer $priority
     * 
     * @return Ink\Contracts\Hooks\FilterManager
     */
    public function flush(int $priority = 10) : FilterManager;

    /**
     * Force compilation of callable mutators passed
     * as arguments to the filter manager
     *
     * @return Ink\Contracts\Hooks\FilterManager
     */
    public function forceCompilation() : FilterManager;

    /**
     * Set the namespace from where mutators may be inferred,
     * instead of passing full namespace each time.
     *
     * @param string $namespace
     * 
     * @return Ink\Contracts\Hooks\FilterManager
     */
    public function setMutatorNamespace(string $namespace) : FilterManager;
}