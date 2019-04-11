<?php

namespace Ink\Contracts\Scribe;

interface Resource
{
    /**
     * Publish the extension resource, for example config
     *
     * @return void
     */
    public function publish(): void;
}
