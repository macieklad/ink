<?php

namespace Ink\Scribe;

use Ink\Contracts\Foundation\Theme;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;

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
        $manifest = [];

        $this->addBuiltInCommands();

        if (file_exists($manifestPath)) {
            $manifest = json_decode(file_get_contents($manifestPath), true);
        }

        if (array_key_exists("commands", $manifest)) {
            $this->addExtensionCommands($manifest["commands"]);
        }
    }

    protected function addBuiltInCommands()
    {
        $this->application->addCommands([]);
    }

    protected function addExtensionCommands(array $commands)
    {
        $safeCommands = [];

        foreach ($commands as $command) {
            if (is_subclass_of($command, Command::class)) {
                array_push($safeCommands, $command);
            } else {
                echo "WARNING: Class {$command} is not an instance of Symfony's Command class, skipping initialization. \n";
            }
        }

        $this->application->addCommands($safeCommands);
    }
}