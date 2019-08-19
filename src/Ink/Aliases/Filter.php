<?php

namespace Ink\Aliases;

use Closure;
use Ink\Contracts\Hooks\FilterManager;

/**
 * Access to FilterManager class
 *
 * @see    FilterManager
 * @method FilterManager name(string $filter)
 * @method void apply(mixed $value, mixed ...$args)
 * @method void use(Closure|string|array $mutator,int $priority = 10,int $acceptedArgs = 1)
 * @method bool exists(mixed $mutator)
 * @method FilterManager detach(mixed $mutators, int $priority = 10)
 * @method FilterManager flush(int $priority = 10)
 * @method FilterManager forceCompilation()
 * @method FilterManager setMutatorNamespace(string $namespace)
 */
class Filter extends Alias
{
    /**
     * Return alias underlying container entry
     *
     * @return string
     */
    public static function getAliasAccessor() 
    {
        return FilterManager::class;
    }
}
