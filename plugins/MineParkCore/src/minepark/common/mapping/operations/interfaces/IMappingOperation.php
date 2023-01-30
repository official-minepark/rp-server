<?php

namespace minepark\common\mapping\operations\interfaces;

use minepark\common\mapping\MappingOptions;

interface IMappingOperation
{
    public function mapProperty(string $propertyName, mixed $source, string $destination): void;

    public function setOptions(MappingOptions $options): void;
}