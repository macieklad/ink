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
     * Container instance for calling transformers
     *
     * @var Psr\Container\ContainerInterface;
     */
    protected $container;

    /**
     *  Decide whether callable transformers passed to filter
     *  should be compiled before being called
     *
     * @var boolean
     */
    protected $forceCompilation = false;

    /**
     * Namespace from where transformers can be
     * inferred without providing namespace 
     *
     * @var string
     */
    protected $transformerNamespace = '';

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
        apply_filters($this->filter, $value, ...$args);

        return $this;
    }

    /**
     * Add a transformer to the filter, with priority and 
     * maximum allowed arguments.
     *
     * @param Closure|string|array $transformer
     * @param integer              $priority
     * @param integer              $acceptedArgs
     * 
     * @return mixed
     */
    public function add(
        $transformer,
        int $priority = 10,
        int $acceptedArgs = 1
    ) {
        $compiledTransformer = $this->compileTransformer($transformer); 

        add_filter($this->filter, $compiledTransformer, $priority, $acceptedArgs);

        return $compiledTransformer;
    }

    /**
     * Compile transformer that was passed inside manager
     * into callback function, by passing it through
     * the service container if it's not callable. 
     *
     * @param mixed $transformer
     * 
     * @return mixed
     */
    protected function compileTransformer($transformer)
    {
        $transformerIsFunction = (
            is_string($transformer) && \function_exists($transformer)
        );

        if (\is_callable($transformer) || $transformerIsFunction) {
            return $this->forceCompilation ? 
                $this->compileCallableTransformer($transformer) : $transformer;
        }

        if (\is_array($transformer)) {
            return $this->compileArrayTransformer($transformer);
        }

        if (\is_string($transformer)) {
            return $this->compileTransformerString($transformer);
        }

        throw new \InvalidArgumentException(
            "The type of passed transformer is not supported by action manager.
             Please change it, maybe it was a typo ? Passed " . gettype($transformer)
        );
    }

    /**
     * Convert action hook into callback
     * by passing it through container
     *
     * @param callable $transformer
     * 
     * @return \Closure
     */
    public function compileCallableTransformer($transformer)
    {
        return function () use ($transformer) { 
            $args = \func_get_args();

            return $this->container->call($transformer, $args);
        };
    }

    /**
     * Compile transformer in form of steps defined
     * inside an array
     *
     * @param array $transformers
     * 
     * @return void
     */
    public function compileArrayTransformer(array $transformers)
    {
        return function () use ($transformers) {
            $args = \func_get_args();
            $value = \array_shift($args);

            foreach ($transformers as $transformer) {
                $value = $this->compileTransformer($transformer)($value, ...$args);
            }
            
            return $value;
        };
    }

    /**
     * Compile transformer in form of Transformer@method
     *
     * @param string $transformer
     * 
     * @return void
     */
    public function compileTransformerString(string $transformer)
    {
        $parts = explode('@', $transformer);
        $transformer = $parts[0];
        $method = $parts[1];

        if (!\class_exists($transformer)) {
            $transformer = $this->transformerNamespace . '\\' . $transformer;
        }

        if (!\class_exists($transformer)) {
            throw new \InvalidArgumentException(
                "Transformer {$transformer} doesn't exist, please specify 
                 existing transformer, or set manager namespace correctly first."
            );
        }

        if (!\is_callable([$transformer, $method])) {
            throw new \InvalidArgumentException(
                "Transformer {$transformer} method {$method} is not callable,
                 it may be a typo, or the method does not exist."
            );
        }

        return function () use ($transformer, $method) {
            $args = \func_get_args();

            return $this->container->call([$transformer, $method], $args);
        };
    }

    /**
     * Check if filter with the given name is defined
     * with any transformers.
     *
     * @param mixed $transformer
     * 
     * @return boolean
     */
    public function exists($transformer) : bool
    {
        return has_filter($this->filter, $transformer);
    }


    /**
     * Detach single or multiple transformers 
     * from the filter, with given priority.
     *
     * @param Closure|string|array $transformers
     * @param integer              $priority
     * 
     * @return Ink\Contracts\Hooks\FilterManager
     */
    public function detach($transformers, int $priority = 10) : FilterManagerContract
    {
        remove_filter($this->filter, $transformers, $priority);

        return $this;
    }

    /**
     * Remove all possible transformers of given
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
     * Force compilation of callable transformers passed
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
     * Set the namespace from where transformers may be inferred,
     * instead of passing full namespace each time.
     *
     * @param string $namespace
     * 
     * @return Ink\Contracts\Hooks\FilterManager
     */
    public function setTransformerNamespace(
        string $namespace
    ) : FilterManagerContract {
        $this->transformerNamespace = $namespace;

        return $this;
    }
}