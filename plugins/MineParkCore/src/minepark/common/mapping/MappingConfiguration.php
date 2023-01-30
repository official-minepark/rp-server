<?php

namespace minepark\common\mapping;

use minepark\common\di\interfaces\FromContext;
use minepark\common\di\interfaces\Singleton;
use minepark\common\di\traits\SingletonArgsTrait;
use minepark\common\di\traits\SingletonTrait;

final class MappingConfiguration implements Singleton, FromContext
{
    use SingletonTrait;
    use SingletonArgsTrait;

    public function __construct(
        Mapper $mapper
    )
    {

    }
}