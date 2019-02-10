<?php

namespace Ink\Config;

class Repository 
{
    /**
     * Array of repository items
     *
     * @var array
     */
    protected $items = [];
    
    /**
     * Undocumented function
     *
     * @param mixed $key
     * @param mixed $value
     * @return void
     */
    public function set($key, $value)
    {
        $keys = is_array($key) ? $key : [$key => $value];

        foreach ($keys as $key => $value) 
        {
            $this->setRecursive($key, $value);
        }
    }

    /**
     * Get single or multiple values from the repository
     *
     * @param mixed $query
     * @return void
     */
    public function get($key = '')
    {
        if (is_array($key)) {
            return $this->getMany($key);
        }

        return $this->retrieveItem($key);
    }

    /**
     * Get multiple items from repository as an array
     *
     * @param array $keys
     * @return void
     */
    protected function getMany(array $keys)
    {
        $values = [];

        foreach ($keys as $key)
        {
            $values[$key] = $this->retrieveItem($key);
        }

        return $values;
    }

    /**
     * Retrieve an item from the repository by its key
     *
     * @param string $key
     * @return void
     */
    protected function retrieveItem(string $key)
    {
        $value = $this->items;

        foreach ($this->parseKey($key) as $index) {
            if (is_array($value) && array_key_exists($index, $value)) {
                $value = $value[$index];
            } else {
                break;
            }
        }

        return $value;
    }

    /**
     * Set key in the repository by its parts
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    protected function setRecursive(string $key, $value)
    {
        $indexes = $this->parseKey($key);
        $finalKey = array_pop($indexes);

        $currentItem = &$this->items;

        foreach ($indexes as $index) 
        {
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
     * Parses query for items into value stack
     */
    protected function parseKey(string $key)
    {
        return explode('.', $key);
    }

}