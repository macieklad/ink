<?php

namespace Ink\Aliases;

use Closure;
use Ink\Contracts\Hooks\ActionManager;

/**
 * ActionManager alias
 *
 * @see    ActionManager
 * @method static ActionManager name(string $action)
 * @method static ActionManager handle(string|Closure|array $with, int $priority = 10, int $acceptedArgs = 1)
 * @method static ActionManager dispatch(mixed ...$args)
 * @method static bool exists(mixed $handler)
 * @method static int count()
 * @method static ActionManager detach(Closure|string|array $handlers, int $priority = 10)
 * @method static ActionManager flush(int $priority = 10)
 * @method static ActionManager setHandlerNamespace(string $namespace)
 * @method static ActionManager forceCompilation()
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
