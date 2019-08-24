<?php

namespace Ink\Foundation\Bootstrap;

use Di\Container as Container;
use DI\DependencyException;
use DI\NotFoundException;

class LoadServices implements KernelCommand
{
    /**
     * Container instance
     *
     * @var Container
     */
    protected $container;

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
     * Read service providers list, and initialize each of them
     *
     * @return void
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function fire()
    {
        $providers = $this->container->get('config')->get('theme.providers', []);

        foreach ($providers as $provider) {
            if (method_exists($provider, 'boot')) {
                $this->container->call([$provider, 'boot']);
            }
        }

        foreach ($providers as $provider) {
            if (method_exists($provider, 'start')) {
                $this->container->call([$provider, 'start']);
            }
        }
    }
}
