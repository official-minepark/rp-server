<?php

namespace minepark\loader;

use minepark as mp;
use minepark\common\di\interfaces\FromContext;
use minepark\common\di\interfaces\Singleton;
use minepark\common\di\traits\SingletonArgsTrait;
use minepark\common\di\traits\SingletonTrait;

class Loader implements Singleton, FromContext
{
    use SingletonTrait, SingletonArgsTrait;

    public static function fromSingletonArgs(
        mp\common\mapping\MappingConfiguration $mappingConfiguration,
        mp\common\mdc\MDC $mdc,
        mp\application\ComponentsLoader $componentsLoader,
        mp\application\ServerCommands $serverCommands
    ): self
    {
        return new self;
    }
}