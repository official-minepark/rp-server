<?php

namespace minepark\application;

use minepark\application\components as components;
use minepark\common\di\Context;
use minepark\common\di\interfaces\FromContext;
use minepark\common\di\interfaces\Singleton;
use minepark\common\di\traits\SingletonArgsTrait;
use minepark\common\di\traits\SingletonTrait;
use minepark\common\utils\ServerInitializationQueue;
use minepark\plugin\MainPlugin;
use SOFe\AwaitGenerator\Await;

class ComponentsLoader implements Singleton, FromContext
{
    use SingletonTrait;
    use SingletonArgsTrait;

    public static function fromSingletonArgs(
        Context $context
    ): self
    {
        return new self(
            $context,
            components: [
                components\Users::class,
                components\world\WorldProtection::class,
                components\AntiCheat::class
            ]
        );
    }

    public function __construct(Context $context, array $components)
    {
        Await::g2c($this->initializeComponents($context, $components));
    }

    private function initializeComponents(Context $context, array $components): \Generator
    {
        $plugin = MainPlugin::getInstanceOrNull($context);

        foreach ($components as $component) {
            $componentInstance = yield from $component::getInstance($context);
            $plugin->getServer()->getPluginManager()->registerEvents($componentInstance, $plugin);
        }
    }
}