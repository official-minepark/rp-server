<?php

namespace minepark\common\mapping\operations;

use minepark\common\mapping\operations\interfaces\MapperAwareOperation;
use minepark\common\mapping\operations\traits\MapperAwareOperationTrait;

class MapToOperation extends DefaultMappingOperation implements MapperAwareOperation
{
    use MapperAwareOperationTrait;

    public function __construct(
        private string $destinationClass
    )
    {
    }

    protected function getSourceValue($source, string $propertyName): mixed
    {
        $value = $this->propertyReader->getProperty(
            $source,
            $propertyName
        );

        return $this->mapper->map($source, $this->destinationClass);
    }
}