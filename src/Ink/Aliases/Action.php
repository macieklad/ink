<?php

namespace Ink\Aliases;

use Ink\Aliases\Alias;
use Ink\Contracts\Hooks\ActionManager;

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
