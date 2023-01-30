<?php

namespace minepark\common\mapping\readers;

use ReflectionClass;

class ObjectPropertyReader implements IPropertyReader
{
    public function hasProperty(mixed $object, string $propertyName): bool
    {
        return property_exists($object, $propertyName);
    }

    public function getProperty(mixed $object, string $propertyName): mixed
    {
        if (isset($object->{$propertyName})) {
            return $object->{$propertyName};
        }

        return null;
    }

    public function getPropertyNames(mixed $object): array
    {
        $reflection = new ReflectionClass($object);

        $properties = [];

        foreach ($reflection->getProperties() as $property) {
            $properties[] = $property->getName();
        }

        return $properties;
    }
}