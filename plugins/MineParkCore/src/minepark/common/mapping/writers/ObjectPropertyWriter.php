<?php

namespace minepark\common\mapping\writers;

class ObjectPropertyWriter implements IPropertyWriter
{
    public function writeProperty(mixed $object, string $propertyName, mixed $value): void
    {
        $object->{$propertyName} = $value;
    }
}