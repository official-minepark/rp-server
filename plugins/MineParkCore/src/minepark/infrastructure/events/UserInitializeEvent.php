<?php

namespace minepark\infrastructure\events;

use minepark\infrastructure\models\UserStatesMapModel;
use pocketmine\event\Event;
use pocketmine\player\Player;
use SOFe\AwaitGenerator\Mutex;

/**
 * NOTE: Event is called after setting profile in UserStatesMapModel.
 */
class UserInitializeEvent extends Event
{
    public function __construct(
        private Player $player,
        private UserStatesMapModel $statesMap,
        private Mutex $initializationMutex
    )
    {}

    public function getPlayer(): Player
    {
        return $this->player;
    }

    public function getStatesMap(): UserStatesMapModel
    {
        return $this->statesMap;
    }

    public function getInitializationMutex(): Mutex
    {
        return $this->initializationMutex;
    }
}