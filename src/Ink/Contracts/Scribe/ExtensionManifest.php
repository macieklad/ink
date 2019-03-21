<?php

namespace Ink\Contracts\Scribe;

interface ExtensionManifest
{
    /**
     * Writes the created manifest to a file
     *
     * @param $location string
     *
     * @return mixed
     */
    public function write(string $location): void;

    /**
     * Loads contents of manifest file
     *
     * @param $location string
     *
     * @return void
     */
    public function loadFrom(string $location): void;

    /**
     * Get array of commands from manifest
     *
     * @return array
     */
    public function commands(): array;

    /**
     * Return an array of resources published by extensions
     *
     * @return array
     */
    public function resources(): array;
}