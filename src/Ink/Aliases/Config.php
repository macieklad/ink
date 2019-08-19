<?php

namespace Ink\Aliases;

use Ink\Contracts\Config\Repository;

/**
 * Access to theme config repository
 *
 * @see    Repository
 * @method static array all()
 * @method static mixed get(string $key, mixed $default)
 * @method static mixed set(string $key, mixed $default)
 * @method static mixed getMultiple(array $items)
 * @method static void  setMultiple(array $items)
 * @method static bool  has(string $key)
 */
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