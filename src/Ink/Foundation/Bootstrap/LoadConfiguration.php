<?php

namespace Ink\Foundation\Bootstrap;

use Ink\Foundation\Theme;
use Ink\Config\Repository;
use Ink\Container\ContainerProxy as Container;

class LoadConfiguration implements KernelCommand
{
    /**
     * Prepare the command
     *
     * @param Container $container
     */
    public function __construct(Theme $theme, Container $container)
    {
        $this->theme = $theme;
        $this->container = $container;
    }

    /**
     * Load theme configuration and place it inside the container 
     *
     * @return void
     */
    public function fire()
    {
        $this->container->set('config', new Repository);
        $configDir = $this->theme->configPath();

        $this->loadFromDirectory($configDir);
    }

    /**
     * Load configuration files from a directory
     *
     * @param string $dir
     * @return void
     */
    protected function loadFromDirectory(string $dir)
    {
        $repository = $this->container->get('config');
        $objects = scandir($dir);

        foreach ($objects as $object) {
            $path = $dir . DIRECTORY_SEPARATOR . $object;

            if (is_file($path)) {
                $config = require_once $path;
                $repository->set(basename($object, ".php"), $config);
            }
        }
    }
}
