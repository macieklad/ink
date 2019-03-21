<?php

namespace Ink\Scribe;

use Ink\Contracts\Scribe\ExtensionManifest as ManifestContract;

class ExtensionManifest implements ManifestContract
{
    protected $commands = [];

    protected $resources = [];

    public function write(string $location): void
    {
        // TODO: Implement write() method.
    }

    public function loadFrom(string $location): void
    {
        // TODO: Implement loadFrom() method.
    }

    public function commands(): array
    {
        // TODO: Implement commands() method.
        return [];
    }

    public function resources(): array
    {
        // TODO: Implement resources() method.
        return [];
    }


}