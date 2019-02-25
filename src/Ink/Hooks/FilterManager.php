<?php

namespace Ink\Hooks;

use Psr\Container\ContainerInterface;
use Ink\Contracts\Hooks\FilterManager as FilterManagerContract;

class FilterManager implements FilterManagerContract
{
    /**
     * Managed filter name
     *
     * @var string
     */
    protected $filter = '';

    /**
     * Container instance for calling mutator
     *
     * @var Psr\Container\ContainerInterface;
     */
    protected $container;

    /**
     *  Decide whether callable mutator passed to filter
     *  should be compiled before being called
     *
     * @var boolean
     */
    protected $forceCompilation = false;

    /**
     * Namespace from where mutator can be
     * inferred without providing namespace 
     *
     * @var string
     */
    protected $mutatorNamespace = '';

    /**
     * Construct the manager for a named filter
     *
     * @param Psr\Container\ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Manage filter with the given name
     *
     * @param string $filter
     * 
     * @return Ink\Contracts\Hooks\FilterManager
     */
    public function name(string $filter) : FilterManagerContract
    {
        $this->filter = $filter;

        return $this;
    }

    /**
     * Apply the filter to the provided value and pass
     * some arguments to it.
     *
     * @param mixed $value
     * @param mixed ...$args
     * 
     * @return void
     */
    public function apply($value, ...$args)
    {
        return apply_filters($this->filter, $value, ...$args);
    }

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
    ) {
        $compiledMutator = $this->compileMutator($mutator); 

        add_filter($this->filter, $compiledMutator, $priority, $acceptedArgs);

        return $compiledMutator;
    }

    /**
     * Compile mutator that was passed inside manager
     * into callback function, by passing it through
     * the service container if it's not callable. 
     *
     * @param mixed $mutator
     * 
     * @return mixed
     */
    protected function compileMutator($mutator)
    {
        $mutatorIsFunction = (
            is_string($mutator) && \function_exists($mutator)
        );

        if (\is_callable($mutator) || $mutatorIsFunction) {
            return $this->forceCompilation ? 
                $this->compileCallableMutator($mutator) : $mutator;
        }

        if (\is_array($mutator)) {
            return $this->compileArrayMutator($mutator);
        }

        if (\is_string($mutator)) {
            return $this->compileMutatorString($mutator);
        }

        throw new \InvalidArgumentException(
            "The type of passed mutator is not supported by action manager.
             Please change it, maybe it was a typo ? Passed " . gettype($mutator)
        );
    }

    /**
     * Convert action hook into callback
     * by passing it through container
     *
     * @param callable $mutator
     * 
     * @return \Closure
     */
    public function compileCallableMutator($mutator)
    {
        return function () use ($mutator) { 
            $args = \func_get_args();

            return $this->container->call($mutator, $args);
        };
    }

    /**
     * Compile mutator in form of steps defined
     * inside an array
     *
     * @param array $mutators
     * 
     * @return void
     */
    public function compileArrayMutator(array $mutators)
    {
        return function () use ($mutators) {
            $args = \func_get_args();
            $value = \array_shift($args);

            foreach ($mutators as $mutator) {
                $value = $this->compileMutator($mutator)($value, ...$args);
            }
            
            return $value;
        };
    }

    /**
     * Compile mutator in form of Mutator@method
     *
     * @param string $mutator
     * 
     * @return void
     */
    public function compileMutatorString(string $mutator)
    {
        $parts = explode('@', $mutator);
        $mutator = $parts[0];
        $method = $parts[1];

        if (!\class_exists($mutator)) {
            $mutator = $this->mutatorNamespace . '\\' . $mutator;
        }

        if (!\class_exists($mutator)) {
            throw new \InvalidArgumentException(
                "Mutator {$mutator} doesn't exist, please specify 
                 existing mutator, or set manager namespace correctly first."
            );
        }

        if (!\is_callable([$mutator, $method])) {
            throw new \InvalidArgumentException(
                "Mutator {$mutator} method {$method} is not callable,
                 it may be a typo, or the method does not exist."
            );
        }

        return function () use ($mutator, $method) {
            $args = \func_get_args();

            return $this->container->call([$mutator, $method], $args);
        };
    }

    /**
     * Check if filter with the given name is defined
     * with any mutators.
     *
     * @param mixed $mutator
     * 
     * @return boolean
     */
    public function exists($mutator) : bool
    {
        return has_filter($this->filter, $mutator);
    }


    /**
     * Detach single or multiple mutators 
     * from the filter, with given priority.
     *
     * @param Closure|string|array $mutators
     * @param integer              $priority
     * 
     * @return Ink\Contracts\Hooks\FilterManager
     */
    public function detach($mutators, int $priority = 10) : FilterManagerContract
    {
        remove_filter($this->filter, $mutators, $priority);

        return $this;
    }

    /**
     * Remove all possible mutators of given
     * priorityfrom the filter.
     *
     * @param integer $priority
     * 
     * @return Ink\Contracts\Hooks\FilterManager
     */
    public function flush(int $priority = 10) : FilterManagerContract
    {
        remove_all_filters($this->filter, $priority);

        return $this;
    }

    /**
     * Force compilation of callable mutators passed
     * as arguments to the filter manager
     *
     * @return Ink\Contracts\Hooks\FilterManager
     */
    public function forceCompilation() : FilterManagerContract
    {
        $this->forceCompilation = true;

        return $this;
    }

    /**
     * Set the namespace from where mutators may be inferred,
     * instead of passing full namespace each time.
     *
     * @param string $namespace
     * 
     * @return Ink\Contracts\Hooks\FilterManager
     */
    public function setMutatorNamespace(
        string $namespace
    ) : FilterManagerContract {
        $this->mutatorNamespace = $namespace;

        return $this;
    }
}