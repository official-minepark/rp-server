<?php

namespace minepark\common\mapping\operations;

use minepark\common\mapping\MappingOptions;

class Ignore extends DefaultMappingOperation
{
    public function mapProperty(string $propertyName, mixed $source, mixed $destination): void
    {
    }

    public function setOptions(MappingOptions $options): void
    {
    }
}