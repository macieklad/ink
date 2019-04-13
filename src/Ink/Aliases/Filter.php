<?php

namespace Ink\Aliases;

use Ink\Aliases\Alias;
use Ink\Contracts\Hooks\FilterManager;

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
