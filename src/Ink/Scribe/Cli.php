<?php

namespace Ink\Scribe;

use Ink\Contracts\Foundation\Theme;
use Symfony\Component\Console\Application;
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

    public function __construct(Theme $theme)
    {
        $this->application = new Application();
        $this->application->setName('Stamp Theme Assistant');
        $this->application->setVersion('alpha');
        $this->theme = $theme;
    }

    public function prepare()
    {
        $this->loadCommands();
    }


    public function run()
    {
        $this->application->run();
    }

    protected function loadCommands()
    {
        $manifestPath = $this->theme->vendorPath('scribe-manifest.json');
        $manifest = $this->theme->container()->get(ExtensionManifest::class);

        $manifest->loadFrom($manifestPath);

        $this->addBuiltInCommands();
    }

    protected function addBuiltInCommands()
    {
        $this->addCommands([
            DiscoverExtensionsCommand::class
        ]);
    }

    protected function addCommands(array $commands)
    {
        $safeCommands = [];

        foreach ($commands as $command) {
            if (is_a($command, Command::class, true)) {
                array_push($safeCommands, $command);
            } else {
                echo "WARNING: Class {$command} is not an instance of Symfony's Command class, skipping initialization. \n";
            }
        }

        $this->application->addCommands(
            array_map(function ($command) {
                return $this->theme->container()->get($command);
            }, $safeCommands)
        );
    }
}