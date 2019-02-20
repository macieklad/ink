<?php

namespace Ink\Foundation\Bootstrap;

use Whoops\Run;
use Ink\Contracts\Config\Repository;
use Ink\Foundation\Bootstrap\KernelCommand;
use Whoops\Handler\PrettyPageHandler as ErrorHandler;

class HandleErrors implements KernelCommand
{
    /**
     * Repository with config
     *
     * @var Repository
     */
    protected $config;

    /**
     * Whoops instance
     *
     * @var Run
     */
    protected $whoops;

    /**
     * Whoops exception handler
     *
     * @var PrettyPageHandler
     */
    protected $exceptionHandler;

    /**
     * Createt the command object
     *
     * @param Run               $whoops
     * @param Repository        $config
     * @param PrettyPageHandler $handler
     */
    public function __construct(
        Run $whoops, 
        Repository $config, 
        ErrorHandler $handler
    ) {
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