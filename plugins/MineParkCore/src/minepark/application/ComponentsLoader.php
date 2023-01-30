<?php

namespace minepark\application;

use minepark\application\components\Users;
use minepark\common\di\interfaces\FromContext;
use minepark\common\di\interfaces\Singleton;
use minepark\common\di\traits\SingletonArgsTrait;
use minepark\common\di\traits\SingletonTrait;
use minepark\plugin\MainPlugin;

class ComponentsLoader implements Singleton, FromContext
{
    use SingletonTrait;
    use SingletonArgsTrait;

    public static function fromSingletonArgs(
        MainPlugin    $mainPlugin,
        Users $users
    ): self
    {
        return new self(
            $mainPlugin,
            components: [
                $users
            ]
        );
    }

    public function __construct(MainPlugin $mainPlugin, array $components)
    {
        foreach ($components as $component) {
            $mainPlugin->getServer()->getPluginManager()->registerEvents($component, $mainPlugin);
        }
    }
}