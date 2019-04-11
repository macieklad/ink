<?php

namespace Ink\Scribe;

use Ink\Contracts\Foundation\Theme;
use Ink\Contracts\Scribe\ThemeAssistant as ThemeAssistantContract;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\Filesystem\Filesystem;

class ThemeAssistant implements ThemeAssistantContract
{
    /**
     * Filesystem instance
     *
     * @var Filesystem
     */
    protected $fs;

    /**
     * Stamp theme instance
     *
     * @var Theme
     */
    protected $theme;

    public function __construct(Filesystem $fs, Theme $theme)
    {
        $this->fs = $fs;
        $this->theme = $theme;
    }

    /**
     * Copy a config file to the theme config directory
     *
     * @param $file string
     * @param $configName string
     *
     * @throws FileNotFoundException
     *
     * @return void
     */
    public function publishConfig(string $file, string $configName = ''): void
    {
        $configName = $configName == '' ? basename($file) : $configName . ".php";

        if ($this->fs->exists($file)) {
            $this->fs->copy($file, $this->theme->configPath($configName), true);
        } else {
            throw new FileNotFoundException($file);
        }
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
        if ($this->fs->exists($file)) {
            $this->fs->copy($file, $this->theme->basePath($path), true);
        } else {
            throw new FileNotFoundException($file);
        }
    }
}
