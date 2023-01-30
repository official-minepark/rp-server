<?php

namespace minepark\infrastructure\events;

use minepark\infrastructure\models\UserStatesMapModel;
use pocketmine\event\Event;
use pocketmine\player\Player;
use SOFe\AwaitGenerator\Mutex;

class UserQuitEvent extends Event
{
    public function __construct(
        private Player $player,
        private UserStatesMapModel $statesMap,
        private Mutex $mutex
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

    public function getMutex(): Mutex
    {
        return $this->mutex;
    }
}