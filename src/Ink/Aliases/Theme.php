<?php

namespace Ink\Aliases;

use Ink\Aliases\Alias;

class Theme extends Alias
{
    /**
     * Return alias underlying container entry
     *
     * @return string
     */
    public static function getAliasAccessor()
    {
        return 'theme';
    }
}
