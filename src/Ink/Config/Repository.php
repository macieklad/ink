<?php

namespace Ink\Config;

use Ink\Contracts\Config\Repository as RepositoryContract;

class Repository implements RepositoryContract
{
    /**
     * Array of repository items
     *
     * @var array
     */
    protected $items;
    
    /**
     * Create the repository with default values
     *
     * @param array $items
     */
    public function __construct(array $items = [])
    {
        $this->items = $items;
    }

    /**
     * Check if a key exists in repository
     *
     * @param string $key
     * 
     * @return boolean
     */
    public function has(string $key): bool 
    { 
        $currentItem = $this->items;

        foreach ($this->parseKey($key) as $part) {
            if (is_array($currentItem) && array_key_exists($part, $currentItem)) {
                $currentItem = $currentItem[$part];
            } else {
                return false;
            }
        }

        return true;
    }

    /**
     * Return all items in repository
     *
     * @return void
     */
    public function all(): array
    {
        return $this->items;
    }

    /**
     * Undocumented function
     *
     * @param mixed $key
     * @param mixed $value
     * 
     * @return void
     */
    public function set(string $key, $value): void
    {
        $this->setRecursive($key, $value);
    }

    /**
     * Get single or multiple values from the repository
     *
     * @param string $key
     * @param mixed  $default
     * 
     * @return void
     */
    public function get(string $key, $default = null)
    {
        return $this->retrieveItem($key, $default);
    }

    /**
     * Get multiple items from repository as an array
     *
     * @param array $items
     * 
     * @return void
     */
    public function getMultiple(array $items): array
    {
        $values = [];

        if ($this->isAssoc($items)) {
            foreach ($items as $key => $default) {
                $values[$key] = $this->retrieveItem($key, $default);
            }
        } else {
            foreach ($items as $key) {
                $values[$key] = $this->retrieveItem($key);
            }
        }
        
        return $values;
    }

    /**
     * Set multiple config items
     *
     * @param array $items
     * 
     * @return void
     */
    public function setMultiple(array $items): void
    {
        foreach ($items as $key => $value) {
            $this->setRecursive($key, $value);
        }
    }

    /**
     * Retrieve an item from the repository by its key
     *
     * @param string $key
     * @param mixed  $default
     * 
     * @return void
     */
    protected function retrieveItem(string $key, $default = null)
    {
        $value = $this->items;

        foreach ($this->parseKey($key) as $index) {
            if (is_array($value) && array_key_exists($index, $value)) {
                $value = $value[$index];
            } else {
                return $default;
            }
        }

        return $value;
    }

    /**
     * Set key in the repository by its parts
     *
     * @param string $key
     * @param mixed  $value
     * 
     * @return void
     */
    protected function setRecursive(string $key, $value)
    {
        $indexes = $this->parseKey($key);
        $finalKey = array_pop($indexes);

        $currentItem = &$this->items;

        foreach ($indexes as $index) {
            if (isset($currentItem[$index])) {
                if (! is_array($currentItem[$index])) {
                    $currentItem[$index] = [];
                }
            } else {
                $currentItem[$index] = [];
            }

            $currentItem = &$currentItem[$index];
        } 

        $currentItem[$finalKey] = $value;
    }

    /**
     * Check if array is associative
     *
     * @param array $arr
     * 
     * @return boolean
     */
    protected function isAssoc(array $arr): bool
    {
        return array_keys($arr) !== range(0, count($arr) - 1);
    }

    /** 
     * Parses query for items into value stack
     * 
     * @param string $key
     * 
     * @return array
     */
    protected function parseKey(string $key)
    {
        return explode('.', $key);
    }

}
