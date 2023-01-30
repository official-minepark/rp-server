<?php

namespace minepark\infrastructure\stores;

use minepark\infrastructure\models\UserStatesMapModel;
use pocketmine\player\Player;
use pocketmine\Server;

class UserStatesMapStore extends DataStore
{
    public function __construct(
        private Server $server
    )
    {}

    public function isUserInitialized(Player $user): bool
    {
        return $this->exists($this->getUserIdentification($user));
    }

    public function findByName(string $name): ?UserStatesMapModel
    {
        foreach ($this->getAll() as $currentStatesMap) {
            if ($currentStatesMap->profile->name === $name) {
                return $currentStatesMap;
            }
        }

        return null;
    }

    public function getForUser(Player $user): ?UserStatesMapModel
    {
        return $this->get($this->getUserIdentification($user));
    }

    public function setForUser(Player $user, UserStatesMapModel $userStatesMapModel): void
    {
        $this->set($this->getUserIdentification($user), $userStatesMapModel);
    }

    public function removeForUser(Player $user): void
    {
        $this->remove($this->getUserIdentification($user));
    }

    private function getUserIdentification(Player $user): string
    {
        return $user->getXuid();
    }
}