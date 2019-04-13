<?php

namespace Ink\Foundation;

class Support
{
    /**
     * Join many paths into single string
     *
     * @return void
     */
    public static function joinPaths()
    {
        $paths = array();

        foreach (func_get_args() as $arg) {
            if ($arg !== '') {
                $paths[] = $arg;
            }
        }

        return preg_replace(
            '#' . DIRECTORY_SEPARATOR . '+#',
            DIRECTORY_SEPARATOR,
            join(DIRECTORY_SEPARATOR, $paths)
        );
    }

}
