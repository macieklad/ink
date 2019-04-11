<?php

namespace Ink\Scribe;

use Ink\Contracts\Scribe\ThemeAssistant as ThemeAssistantContract;

class ThemeAssistant implements ThemeAssistantContract
{
    /**
     * Copy a config file to the theme config directory
     *
     * @param $file string
     * @param $configName string
     *
     * @return void
     */
    public function publishConfig(string $file, string $configName = ''): void
    {

    }

    /**
     * Copy any file to a directory relative to the theme root
     *
     * @param $file string
     * @param $path string
     *
     * @return void
     */
    public function publishResource(string $file, string $path = ''): void
    {

    }
}