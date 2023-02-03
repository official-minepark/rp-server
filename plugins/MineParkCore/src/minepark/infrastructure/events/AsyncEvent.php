<?php

namespace minepark\infrastructure\events;

use pocketmine\event\Event;
use pocketmine\event\EventPriority;
use pocketmine\event\HandlerListManager;
use pocketmine\event\player\PlayerEvent;
use pocketmine\world\generator\Generator;
use SOFe\AwaitGenerator\Await;
use SOFe\AwaitGenerator\GeneratorUtil;
use SOFe\AwaitGenerator\Mutex;

abstract class AsyncEvent extends Event
{
    private Mutex $mutex;

    public function __construct()
    {
        $this->mutex = new Mutex;
    }

    public function addToQueue(\Generator $generator): void
    {
        Await::g2c($this->mutex->run($generator));
    }

    public function awaitForFinish(): \Generator
    {
        return yield from $this->mutex->run(GeneratorUtil::empty());
    }

    public function call(): void
    {
        $handlerList = HandlerListManager::global()->getListFor(get_class($this));

        foreach (EventPriority::ALL as $priority){
            $currentList = $handlerList;

            while ($currentList !== null) {
                foreach ($currentList->getListenersByPriority($priority) as $registration) {
                    $registration->callEvent($this);
                }

                $currentList = $currentList->getParent();
            }
        }
    }
}