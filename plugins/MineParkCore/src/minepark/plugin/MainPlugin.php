<?php

namespace minepark\plugin;

use Generator;
use minepark\common\di\Context;
use minepark\common\di\interfaces\Singleton;
use minepark\common\di\traits\SingletonTrait;
use minepark\common\utils\ServerInitializationQueue;
use minepark\loader\Loader;
use pocketmine\plugin\PluginBase;
use SOFe\AwaitGenerator\Await;
use SOFe\AwaitGenerator\GeneratorUtil;
use SOFe\AwaitStd\AwaitStd;

final class MainPlugin extends PluginBase implements Singleton
{
    use SingletonTrait;

    private bool $initialized = false;

    public function isInitialized(): bool
    {
        return $this->initialized;
    }

    protected function onEnable(): void
    {
        $context = new Context();
        $context->store($this);
        $context->store(AwaitStd::init($this));

        ServerInitializationQueue::initialize();

        Await::g2c(ServerInitializationQueue::getMutex()->runClosure(function () use ($context): Generator {
            yield from self::getAwaitStd($context)->sleep(0);

            yield from Loader::getInstance($context);

            Await::g2c(ServerInitializationQueue::getMutex()->run($this->onPostInitialize()));
        }));
    }

    public static function getAwaitStd(Context $context)
    {
        return $context->getOrNull(AwaitStd::class);
    }

    private function onPostInitialize(): Generator
    {
        $this->getLogger()->info("MinePark Core system successfully initialized!");
        $this->initialized = true;
        yield from GeneratorUtil::empty();
    }
}