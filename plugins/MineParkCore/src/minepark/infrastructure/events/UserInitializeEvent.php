<?php

namespace minepark\infrastructure\events;

use minepark\infrastructure\models\UserStatesMapModel;
use pocketmine\event\Event;
use pocketmine\player\Player;
use SOFe\AwaitGenerator\Mutex;

/**
 * NOTE: Event is called after setting profile in UserStatesMapModel.
 */
class UserInitializeEvent extends AsyncUserEvent
{
    public function __construct(
        private Player $user,
        private UserStatesMapModel $statesMap
    )
    {
        parent::__construct($this->user);
    }

    public function getStatesMap(): UserStatesMapModel
    {
        return $this->statesMap;
    }
}