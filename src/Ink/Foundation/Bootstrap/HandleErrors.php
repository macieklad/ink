<?php

namespace Ink\Foundation\Bootstrap;

use Ink\Foundation\Bootstrap\KernelCommand;
use Whoops\Run;
use Whoops\Handler\PrettyPageHandler;
use Ink\Config\Repository;

class HandleErrors implements KernelCommand 
{
    protected $config;


    protected $whoops;


    protected $exceptionHandler;

    public function __construct(Repository $config, Run $whoops, PrettyPageHandler $handler)
    {
        $this->whoops = $run;
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
        if ($this->config->get('devMode', true)) {
            $this->whoops->pushHandler($this->exceptionHandler);
            $this->whoops->register();
        }
    }
}