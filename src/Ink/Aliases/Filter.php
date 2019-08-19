<?php

namespace Ink\Aliases;

use Closure;
use Ink\Contracts\Hooks\FilterManager;

/**
 * Access to FilterManager class
 *
 * @see    FilterManager
 * @method static FilterManager name(string $filter)
 * @method static void apply(mixed $value, mixed ...$args)
 * @method static void use(Closure|string|array $mutator,int $priority = 10,int $acceptedArgs = 1)
 * @method static bool exists(mixed $mutator)
 * @method static FilterManager detach(mixed $mutators, int $priority = 10)
 * @method static FilterManager flush(int $priority = 10)
 * @method static FilterManager forceCompilation()
 * @method static FilterManager setMutatorNamespace(string $namespace)
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
