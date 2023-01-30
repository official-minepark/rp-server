<?php

namespace minepark\common\mapping;

use minepark\common\mapping\readers\IPropertyReader;
use minepark\common\mapping\writers\IPropertyWriter;

final class MappingOptions
{
    public function __construct(
        private IPropertyReader $propertyReader,
        private IPropertyWriter $propertyWriter
    )
    {
    }

    public function getPropertyReader(): IPropertyReader
    {
        return $this->propertyReader;
    }

    public function getPropertyWriter(): IPropertyWriter
    {
        return $this->propertyWriter;
    }
}