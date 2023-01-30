<?php

namespace minepark\common\mapping\readers;

class ArrayPropertyReader implements IPropertyReader
{
    public function hasProperty(mixed $object, string $propertyName): bool
    {
        return array_key_exists($propertyName, $object);
    }

    public function getProperty(mixed $object, string $propertyName)
    {
        return $object[$propertyName];
    }

    public function getPropertyNames(mixed $object): array
    {
        return array_keys($object);
    }
}