<?php

namespace minepark\common\mapping\operations\interfaces;

use minepark\common\mapping\Mapper;

interface MapperAwareOperation
{
    public function setMapper(Mapper $mapper): void;
}