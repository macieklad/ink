<?php

namespace Ink\Foundation\Bootstrap;

use Psr\Container\ContainerInterface as Container;

class LoadServices implements KernelCommand
{
    /**
     * Prepare the command
     *
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Read service providers list, and initalize each of them
     *
     * @return void
     */
    public function fire()
    {
        $providers = $this->container->get('config')->get('theme.providers', []);

        foreach ($providers as $provider) {
            $this->container->call([$provider, 'boot']);
        }
    }
}
