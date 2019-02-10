<?php

namespace Ink\Aliases;

use Ink\Aliases\Alias;

class Config extends Alias
{
    public static function getAliasAccessor()
    {
        return 'config';
    }
}