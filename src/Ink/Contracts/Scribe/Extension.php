<?php

namespace Ink\Contracts\Scribe;

use Ink\Scribe\Cli;

interface Extension
{
    /**
     * Add extension functionality to scribe
     *
     * @return mixed
     */
    public function prepare(Cli $scribe);

    /**
     * Attach the extension to the theme, by doing some tasks
     * such as provider registration or config publication
     * TODO: Create some kind of interface for managing theme resources
     *
     * @return void
     */
    public function install();
}