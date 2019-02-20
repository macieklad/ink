<?php

namespace Ink\Foundation\Bootstrap;

interface KernelCommand
{
    /**
     * Fire the command, register the code it executes
     *
     * @return void
     */
    public function fire();
}