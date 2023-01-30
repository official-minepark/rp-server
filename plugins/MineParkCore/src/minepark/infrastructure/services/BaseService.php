<?php

namespace minepark\infrastructure\services;

use minepark\common\di\interfaces\FromContext;
use minepark\common\di\interfaces\Singleton;
use minepark\common\di\traits\SingletonArgsTrait;
use minepark\common\di\traits\SingletonTrait;

abstract class BaseService implements Singleton, FromContext
{
    use SingletonTrait;
    use SingletonArgsTrait;
}