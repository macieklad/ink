<?php

namespace Ink\Contracts\Scribe;

interface ExtensionManifest
{
    /**
     * Writes the created manifest to a file
     *
     * @param string $location
     *
     * @return mixed
     */
    public function write(string $location): void;

    /**
     * Loads contents of manifest file
     *
     * @param string $location
     *
     * @return void
     */
    public function loadFrom(string $location): void;

    /**
     * Loads manifest from array
     *
     * @param array $manifest
     *
     * @return void
     */
    public function load(array $manifest): void;

    /**
     * Load the extension from schema, extracted
     * from composer file "extra" fields
     *
     * @param array $extension
     *
     * @return void
     */
    public function addExtension(array $extension): void;

    /**
     * Get array of commands from manifest
     *
     * @return array
     */
    public function commands(): array;

    /**
     * Return an array of resources publishers by extensions
     *
     * @return array
     */
    public function resources(): array;
}
