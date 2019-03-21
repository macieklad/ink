<?php

namespace Ink\Scribe;

use Ink\Contracts\Foundation\Theme;

class DiscoverExtensions
{
    /**
     * Discover the stamp extensions after composer autoload dump
     *
     * @param Theme $theme
     *
     * @return void
     */
    public static function postAutoloadDump(Theme $theme): void
    {
        $vendorDir= $theme->vendorPath();
        $composerDir = $vendorDir . DIRECTORY_SEPARATOR . "composer";
        $installed = $composerDir . DIRECTORY_SEPARATOR . "installed.json";

        if (file_exists($installed)) {
            static::buildExtensionManifest(
                json_decode(file_get_contents($installed), true),
                $vendorDir
            );
        }
    }

    /**
     * Build the stamp extension manifest from array of composer
     * package definition files
     *
     * @param array $packages
     * @param string $location
     *
     * @return void
     */
    protected static function buildExtensionManifest(array $packages, string $location): void
    {
        $manifest = new ExtensionManifest();

        foreach ($packages as $package) {
            if (array_key_exists("extra", $package)) {
                $extra = $package["extra"];

                if (array_key_exists("stamp", $extra)) {
                    $manifest->addExtension($extra["stamp"]);
                }
            }
        }

        $manifest->write($location);
    }
}