<?php

namespace Ink\Scribe;

use Ink\Contracts\Foundation\Theme;
use Ink\Contracts\Config\Repository;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Ink\Contracts\Scribe\ThemeAssistant as ThemeAssistantContract;

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

    /**
     * Theme config
     *
     * @var Repository
     */
    protected $config;

    /**
     * Initialize the theme assistant object
     *
     * @param Theme      $theme
     * @param Filesystem $fs
     * @param Repository $config
     */
    public function __construct(Theme $theme, Filesystem $fs, Repository $config)
    {
        $this->theme = $theme;
        $this->fs = $fs;
        $this->config = $config;
    }

    /**
     * Copy a config file to the theme config directory
     *
     * @param string $file
     * @param string $configName
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
     * @param string $file
     * @param string $path
     *
     * @return void
     */
    public function publishResource(string $file, string $path = ''): void
    {
        $destPath = $path == '' ? basename($file) : $path . "/" . basename($file);

        if ($this->fs->exists($file)) {
            $this->fs->copy($file, $this->theme->basePath($destPath), true);
        } else {
            throw new FileNotFoundException($file);
        }
    }

    /**
     * Add aliases to theme before initial load
     *
     * @param array $aliases
     *
     * @return void
     */
    public function registerAliases(array $aliases): void
    {
        $registeredAliases = $this->config->get('aliases', []);

        $this->config->set('aliases', array_merge($registeredAliases, $aliases));
    }

    /**
     * Add providers to theme before initial load
     *
     * @param array $providers
     *
     * @return void
     */
    public function registerProviders(array $providers): void
    {
        $registeredProviders = $this->config->get('theme.providers', []);

        $this->config->set('theme.providers', array_merge($registeredProviders, $providers));
    }
}
