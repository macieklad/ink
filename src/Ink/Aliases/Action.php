<?php

namespace Ink\Aliases;

use Closure;
use Ink\Contracts\Hooks\ActionManager;

/**
 * ActionManager alias
 * @see ActionManager
 * @method ActionManager name(string $action)
 * @method ActionManager handle(string|Closure|array $with, int $priority = 10, int $acceptedArgs = 1)
 * @method ActionManager dispatch(mixed ...$args)
 * @method bool exists(mixed $handler)
 * @method int count()
 * @method ActionManager detach(Closure|string|array $handlers, int $priority = 10)
 * @method ActionManager flush(int $priority = 10)
 * @method ActionManager setHandlerNamespace(string $namespace)
 * @method ActionManager forceCompilation()
 */
class Action extends Alias
{
    /**
     * Return alias underlying container entry
     *
     * @return string
     */
    public static function getAliasAccessor() 
    {
        return ActionManager::class;
    }
}
