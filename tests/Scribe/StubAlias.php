<?php

namespace Tests\Scribe;

use Ink\Aliases\Alias;

class StubAlias extends Alias
{
    /**
     * This is stub alias, pointing to the theme object
     *
     * @return string
     */
    protected static function getAliasAccessor()
    {
        return 'theme';
    }
}