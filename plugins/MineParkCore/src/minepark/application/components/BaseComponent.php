<?php

namespace minepark\application\components;

use minepark\common\di\interfaces\FromContext;
use minepark\common\di\interfaces\Singleton;
use minepark\common\di\traits\SingletonArgsTrait;
use minepark\common\di\traits\SingletonTrait;
use pocketmine\event\Listener;

abstract class BaseComponent implements Singleton, FromContext, Listener
{
    use SingletonTrait;
    use SingletonArgsTrait;
}