<?php

namespace minepark\common\mapping\writers;

interface IPropertyWriter
{
    public function writeProperty(mixed $object, string $propertyName, mixed $value): void;
}