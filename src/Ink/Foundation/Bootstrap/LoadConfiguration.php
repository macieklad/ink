<?php

namespace Ink\Foundation\Bootstrap;

use Ink\Config\Repository;
use Ink\Contracts\Foundation\Theme;

class LoadConfiguration implements KernelCommand
{
    /**
     * Prepare the command
     *
     * @param Container $container
     */
    public function __construct(Theme $theme)
    {
        $this->theme = $theme;
        $this->config = $theme[Repository::class];
    }

    /**
     * Load theme configuration and place it inside the container 
     *
     * @return void
     */
    public function fire()
    {
        $this->theme['config'] = $this->config;
        $configDir = $this->theme->configPath();

        $this->loadFromDirectory($configDir);
    }

    /**
     * Load configuration files from a directory
     *
     * @param  string $dir
     * @return void
     */
    protected function loadFromDirectory(string $dir)
    {
        $objects = scandir($dir);

        foreach ($objects as $object) {
            $path = $dir . DIRECTORY_SEPARATOR . $object;

            if (is_file($path)) {
                $config = include_once $path;
                $this->config->set(basename($object, ".php"), $config);
            }
        }
    }
}
