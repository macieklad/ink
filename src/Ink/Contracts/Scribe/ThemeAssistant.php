<?php

namespace Ink\Contracts\Scribe;

interface ThemeAssistant
{
    /**
     * Copy a config file to the theme config directory
     *
     * @param string $file
     * @param string $configName
     *
     * @return void
     */
    public function publishConfig(string $file, string $configName = ""): void;

    /**
     * Copy any file to a directory relative to the theme root
     *
     * @param string $file
     * @param string $path
     *
     * @return void
     */
    public function publishResource(string $file, string $path = ""): void;
}
