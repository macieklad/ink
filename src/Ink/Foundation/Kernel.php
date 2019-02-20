<?php

namespace Ink\Foundation;

use Ink\Foundation\Theme;
use Psr\Container\ContainerInterface as Container;

class Kernel
{
    /**
     * Container instance
     *
     * @var Container
     */
    protected $container;

    /**
     * Initialize the kernel
     *
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Execute kernel commands
     *
     * @param array $commandSet
     * 
     * @return void
     */
    public function executeCommands(array $commandSet)
    {
        foreach ($commandSet as $command) {
            $this->container->call([$command, 'fire']);
        }
    }
}