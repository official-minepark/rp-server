<?php

namespace minepark\infrastructure\events;

use pocketmine\player\Player;

abstract class AsyncUserEvent extends AsyncEvent
{
    public function __construct(
        private Player $user
    )
    {
        parent::__construct();
    }

    public function getUser(): Player
    {
        return $this->user;
    }

    public function checkIfUserOnline(): bool
    {
        return false;
    }

    public function addToQueue(\Generator $generator): void
    {
        $generatorAdditionalLayer = function() use($generator) : \Generator {
            if (!$this->user->isOnline() and $this->checkIfUserOnline()) {
                return;
            }

            yield from $generator;
        };

        parent::addToQueue($generatorAdditionalLayer());
    }
}