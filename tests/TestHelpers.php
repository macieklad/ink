<?php

namespace Tests;

class TestHelpers
{
    /**
     * Get protected or private property from an object
     *
     * @param $object
     * @param $propertyName
     * @return mixed
     */
    public static function getProperty($object, $propertyName)
    {
        $reflection = new ReflectionClass($object);
        $property = $reflection->getProperty($propertyName);
        $property->setAccessible(true);
        return $property->getValue($object);
    }
}