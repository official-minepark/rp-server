<?php

namespace minepark\infrastructure\stores;

use minepark\infrastructure\models\UserStatesMapModel;
use pocketmine\player\Player;
use pocketmine\Server;
use SOFe\AwaitGenerator\GeneratorUtil;
use SOFe\AwaitGenerator\Mutex;

class UserStatesMapStore extends DataStore
{
    public function isUserInitialized(Player $user): bool
    {
        return $this->exists($this->getUserIdentification($user)) and $this->get($this->getUserIdentification($user)) instanceof UserStatesMapModel;
    }

    public function findById(int $userId): ?UserStatesMapModel
    {
        foreach ($this->getAll() as $currentStatesMap) {
            if ($currentStatesMap instanceof Mutex) {
                continue;
            }

            if ($currentStatesMap->profile->id === $userId) {
                return $currentStatesMap;
            }
        }

        return null;
    }

    public function findByName(string $userName): ?UserStatesMapModel
    {
        foreach ($this->getAll() as $currentStatesMap) {
            if ($currentStatesMap instanceof Mutex) {
                continue;
            }

            if ($currentStatesMap->profile->name === $userName) {
                return $currentStatesMap;
            }
        }

        return null;
    }

    public function getUser(Player $user): ?UserStatesMapModel
    {
        return $this->get($this->getUserIdentification($user));
    }

    public function getUserAsync(Player $user): \Generator
    {
        $data = $this->get($this->getUserIdentification($user));

        if ($data instanceof Mutex) {
            yield from $data->run(GeneratorUtil::empty());
            return yield from $this->getUserAsync($user);
        }

        return $data;
    }

    public function setUser(Player $user, UserStatesMapModel|Mutex $data): void
    {
        $this->set($this->getUserIdentification($user), $data);
    }

    public function removeUser(Player $user): void
    {
        $this->remove($this->getUserIdentification($user));
    }

    private function getUserIdentification(Player $user): string
    {
        return $user->getXuid();
    }
}