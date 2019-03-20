<?php

namespace Ink\Scribe;

use Ink\Contracts\Foundation\Theme;
use Symfony\Component\Console\Application;

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
        $config = $this->theme['config'];
        $commands = array_merge([], $config->get('console.commands', []));

        $this->application->addCommands($commands);
    }


    public function run()
    {
        $this->application->run();
    }
}