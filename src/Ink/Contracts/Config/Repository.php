<?php

namespace Ink\Contracts\Config;

interface Repository
{
    /**
     * Return whole configuration from repository
     *
     * @return array
     */
    public function all(): array;

    /**
     * Return specific value from repository
     *
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public function get(string $key, $default = null);

    /**
     * Set a key inside the repository
     *
     * @param string $key
     * @param mixed  $value
     * 
     * @return void
     */
    public function set(string $key, $value): void;

    /**
     * Get multiple values from repository
     *
     * @param array $items
     *
     * @return array
     */
    public function getMultiple(array $items): array;

    /**
     * Set multiple keys passed as an array
     *
     * @param array $items
     * 
     * @return void
     */
    public function setMultiple(array $items): void;

    /**
     * Test if entry exists inside repository
     * 
     * @param string $key
     * 
     * @return bool
     */
    public function has(string $key): bool;
}
