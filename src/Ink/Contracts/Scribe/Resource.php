<?php

namespace Ink\Contracts\Scribe;

interface Resource
{
    /**
     * Publish the extension resource, for example config
     *
     * @param $location string
     *
     * @return void
     */
    public function publish(): void;
}
