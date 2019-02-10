<?php

namespace Ink\Foundation;

use Ink\Foundation\Theme;
use Ink\Container\ContainerProxy as Container;

class Kernel 
{
    /**
     * Global theme instance
     *
     * @var Stamp\Theme
     */
    protected $theme;

    /**
     * Initialize the kernel
     *
     * @param Theme $theme
     */
    public function __construct(Theme $theme)
    {
        $this->theme = $theme;
    }

    /**
     * Execute kernel commands
     *
     * @param array $commandSet
     * @return void
     */
    public function executeCommands(array $commandSet)
    {
        $container = Container::getInstance();

        foreach ($commandSet as $command)
        {
            $container->call([$command, 'fire']);
        }
    }
}