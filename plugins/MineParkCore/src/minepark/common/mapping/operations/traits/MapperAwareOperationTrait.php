<?php

namespace minepark\common\mapping\operations\traits;

use minepark\common\mapping\Mapper;

trait MapperAwareOperationTrait
{
    protected Mapper $mapper;

    public function setMapper(Mapper $mapper): void
    {
        $this->mapper = $mapper;
    }
}