<?php

namespace Ink\Scribe;

use Composer\Script\Event;

class DiscoverExtensions
{
    public static function postAutoloadDump(Event $event)
    {
        $vendorDir= $event->getComposer()->getConfig()->get('vendor-dir');
        $composerDir = $vendorDir . DIRECTORY_SEPARATOR . "composer";
        $installed = $composerDir . DIRECTORY_SEPARATOR . "installed.json";

        if (file_exists($installed)) {
            static::buildExtensionManifest(
                json_decode(file_get_contents($installed), true)
            );
        }
    }

    protected static function buildExtensionManifest(array $packages)
    {
        $manifest = new ExtensionManifest();

        foreach ($packages as $package) {
            if (array_key_exists("extra", $package)) {

            }
        }
    }
}