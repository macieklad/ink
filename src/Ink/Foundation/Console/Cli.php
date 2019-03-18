<?php

namespace Ink\Foundation\Console;

use Ink\Contracts\Config\Repository;
use Ink\Foundation\Theme;
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

    public function __construct(string $themeBase)
    {
        $this->application = new Application();
        $this->application->setName('Stamp Theme Assistant');
        $this->application->setVersion('0.1.0');

        $this->theme = new Theme($themeBase);
    }

    public function prepare()
    {
        $config = $this->theme->container()->get(Repository::class);
        $commands = array_merge([], $config->get('console.commands', []));

        $this->application->addCommands($commands);
    }


    public function run()
    {
        $this->application->run();
    }
}