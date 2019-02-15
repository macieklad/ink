<?php

namespace Ink\Foundation\Bootstrap;

use Whoops\Run;
use Ink\Contracts\Config\Repository;
use Whoops\Handler\PrettyPageHandler;
use Ink\Foundation\Bootstrap\KernelCommand;

class HandleErrors implements KernelCommand 
{
    protected $config;


    protected $whoops;


    protected $exceptionHandler;

    public function __construct(Repository $config, Run $whoops, PrettyPageHandler $handler)
    {
        $this->whoops = $whoops;
        $this->config = $config;
        $this->exceptionHandler = $handler;
    }

    /**
     * Use whoops for error handling in dev mode
     *
     * @return void
     */
    public function fire()
    {
        if ($this->config->get('theme.devMode', true)) {
            $this->whoops->pushHandler($this->exceptionHandler);
            $this->whoops->register();
        }
    }
}