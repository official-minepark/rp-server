<?php

namespace minepark\common\mapping\operations;

class SetToOperation extends DefaultMappingOperation
{
    public function __construct(
        private mixed $value
    )
    {
    }

    protected function canMapProperty(string $propertyName, mixed $source): bool
    {
        return true;
    }

    protected function getSourceValue($source, string $propertyName)
    {
        return $this->getValue();
    }

    public function getValue(): mixed
    {
        return $this->value;
    }
}