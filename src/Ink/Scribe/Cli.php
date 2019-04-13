<?php

namespace Ink\Scribe;

use Ink\Tests\Scribe\StubCommand;
use Psr\Log\LoggerInterface;
use Ink\Contracts\Foundation\Theme;
use Symfony\Component\Console\Application;
use Ink\Contracts\Scribe\ExtensionManifest;
use Symfony\Component\Console\Command\Command;
use Ink\Foundation\Console\PublishResourcesCommand;
use Ink\Foundation\Console\DiscoverExtensionsCommand;

class Cli extends Application
{
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
     * Logger instance
     *
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Construct the scribe cli
     *
     * @param Theme           $theme
     * @param LoggerInterface $logger
     */
    public function __construct(Theme $theme, LoggerInterface $logger)
    {
        $this->theme = $theme;
        $this->logger = $logger;

        parent::__construct('Stamp Theme Assistant', '0.1.0');
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
        $manifestPath = $this->theme->vendorPath('stamp-manifest.json');
        $this->manifest = $this->theme->container()->get(ExtensionManifest::class);
        $this->theme->container()->set(ExtensionManifest::class, $this->manifest);

        $this->manifest->loadFrom($manifestPath);


        $this->loadCommands();
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
        $this->addExtensionCommands($this->manifest->commands());
    }

    /**
     * Add built-in commands of the scribe cli
     *
     * @return void
     */
    protected function addBuiltInCommands(): void
    {
        $commands = [
            DiscoverExtensionsCommand::class,
            PublishResourcesCommand::class
        ];

        $this->addCommands(
            array_map(
                function ($command) {
                    return $this->theme->container()->get($command);
                },
                $commands
            )
        );
    }

    /**
     * Add any commands to the cli
     *
     * @param array $commands
     *
     * @return void
     */
    protected function addExtensionCommands(array $commands): void
    {
        $safeCommands = [];

        foreach ($commands as $command) {
            if (is_subclass_of($command, Command::class, true)) {
                array_push($safeCommands, $command);
            } else {
                $this->logger->warning(
                    "Class {$command} is not an instance " .
                    "of symfony command interface"
                );
            }
        }

        $this->addCommands(
            array_map(
                function ($command) {
                    return $this->theme->container()->get($command);
                },
                $safeCommands
            )
        );
    }
}
