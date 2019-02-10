<?php

namespace Ink\Aliases;

use Ink\Aliases\Alias;

class Route extends Alias 
{
    public static function getAliasAccessor() 
    {
        return 'router';
    }
}