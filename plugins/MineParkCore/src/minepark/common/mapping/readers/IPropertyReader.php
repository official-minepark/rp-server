<?php

namespace minepark\common\mapping\readers;

interface IPropertyReader
{
    public function getPropertyNames(mixed $object): array;

    public function hasProperty(mixed $object, string $propertyName);

    public function getProperty(mixed $object, string $propertyName);
}