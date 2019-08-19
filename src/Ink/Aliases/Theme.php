<?php

namespace Ink\Aliases;

use DI\Container;
use Ink\Contracts\Foundation\Theme as ThemeContract;

/**
 * Alias with access to Theme class
 *
 * @see    ThemeContract
 * @method static string basePath(string $path = '')
 * @method static string configPath(string $path = '')
 * @method static string vendorPath(string $path = '')
 * @method static Container container()
 */
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
