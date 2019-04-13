<?php

namespace Ink\Aliases;

use Ink\Aliases\Alias;

class Config extends Alias
{
    /**
     * Return alias underlying container entry
     *
     * @return string
     */
    public static function getAliasAccessor()
    {
        return 'config';
    }
}
