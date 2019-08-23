<?php

namespace Ink\Foundation\Bootstrap;

use Ink\Contracts\Foundation\Theme;
use Ink\Contracts\Scribe\Hook;
use Ink\Scribe\ExtensionManifest;
use Psr\Container\ContainerInterface;

class HookExtensions implements KernelCommand
{
    /**
     * Theme instance
     *
     * @var Theme
     */
    protected $theme;

    /**
     * Prepare the command
     *
     * @param Theme              $theme
     * @param ContainerInterface $container
     * @param ExtensionManifest  $manifest
     */
    public function __construct(Theme $theme, ContainerInterface $container, ExtensionManifest $manifest)
    {
        $this->manifest = $manifest;
        $this->theme = $theme;
        $this->container = $container;
    }

    /**
     * Read service providers list, and initalize each of them
     *
     * @return void
     */
    public function fire()
    {
        $this->manifest->loadFrom(
            $this->theme->vendorPath('stamp-manifest.json')
        );

        foreach ($this->manifest->hooks() as $hook) {
            if (is_a($hook, Hook::class, true)) {
                /**
                 * Extension hook class
                 *
                 * @var Hook $hook
                */
                $hook = $this->container->get($hook);

                $hook->attach();
            }
        }
    }
}
