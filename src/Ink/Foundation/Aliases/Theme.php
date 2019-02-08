<?php

namespace Stamp\Aliases;

use Stamp\Aliases\Alias;

class Theme extends Alias
{
    public static function getAliasAccessor()
    {
        return 'theme';
    }
}