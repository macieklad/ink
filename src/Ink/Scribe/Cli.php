<?php

namespace Ink\Scribe;

use Ink\Contracts\Foundation\Theme;
use Ink\Foundation\Console\PublishResourcesCommand;
use Symfony\Component\Console\Application;
use Ink\Contracts\Scribe\ExtensionManifest;
use Symfony\Component\Console\Command\Command;
use Ink\Foundation\Console\DiscoverExtensionsCommand;

class Cli
{
    /**
     * Symfony application instance
     *
     * @var Application;
     */
    protected $application;

    /**
     * Theme instance
     *
     * @var Theme;
     */
    protected $theme;

    /**
     * Installed extensions manifest.
     *
     * @var \Ink\Contracts\Scribe\ExtensionManifest
     */
    protected $manifest;

    /**
     * Initialize new scribe instance, by wrapping symfony application.
     *
     * @param Theme $theme
     */
    public function __construct(Theme $theme)
    {
        $this->application = new Application();
        $this->application->setName('Stamp Theme Assistant');
        $this->application->setVersion('alpha');
        $this->theme = $theme;
    }

    /**
     * Prepare the cli to be run.
     *
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     *
     * @return void
     */
    public function prepare(): void
    {
        $manifestPath = $this->theme->vendorPath('scribe-manifest.json');
        $this->manifest = $this->theme->container()->get(ExtensionManifest::class);
        $this->theme->container()->set(ExtensionManifest::class, $this->manifest);

        $this->manifest->loadFrom($manifestPath);

        $this->loadCommands();
    }


    /**
     * Fire the CLI
     *
     * @throws \Exception
     *
     * @return void
     */
    public function run(): void
    {
        $this->application->run();
    }

    /**
     * Load commands provided to the cli, both the built-ins
     * and the ones provided by extensions.
     *
     * @return void
     */
    protected function loadCommands(): void
    {
        $this->addBuiltInCommands();
        $this->addCommands($this->manifest->commands());
    }

    /**
     * Add built-in commands of the scribe cli
     *
     * @return void
     */
    protected function addBuiltInCommands(): void
    {
        $this->addCommands(
            [
                DiscoverExtensionsCommand::class,
                PublishResourcesCommand::class
            ]
        );
    }

    /**
     * Add any commands to the cli
     *
     * @param array $commands
     *
     * @return void
     */
    protected function addCommands(array $commands): void
    {
        $safeCommands = [];

        foreach ($commands as $command) {
            if (is_a($command, Command::class, true)) {
                array_push($safeCommands, $command);
            } else {
                echo "WARNING: Class {$command} is not an instance of Symfony's 
                      Command class, skipping initialization. \n";
            }
        }

        $this->application->addCommands(
            array_map(
                function ($command) {
                    return $this->theme->container()->get($command);
                }, $safeCommands
            )
        );
    }
}
