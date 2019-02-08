<?php

namespace Stamp\Aliases;

use Stamp\Aliases\Alias;

class Route extends Alias 
{
    public static function getAliasAccessor() 
    {
        return 'router';
    }
}