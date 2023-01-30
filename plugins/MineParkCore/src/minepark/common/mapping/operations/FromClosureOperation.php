<?php

namespace minepark\common\mapping\operations;

use Closure;
use minepark\common\mapping\operations\interfaces\MapperAwareOperation;
use minepark\common\mapping\operations\traits\MapperAwareOperationTrait;

class FromClosureOperation extends DefaultMappingOperation implements MapperAwareOperation
{
    use MapperAwareOperationTrait;

    public function __construct(
        private Closure $valueClosure
    )
    {
    }

    public function canMapProperty(string $propertyName, mixed $source): bool
    {
        return true;
    }

    protected function getSourceValue($source, string $propertyName)
    {
        return ($this->getValueClosure())($source, $this->mapper);
    }

    public function getValueClosure(): Closure
    {
        return $this->valueClosure;
    }
}