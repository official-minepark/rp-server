<?php

namespace minepark\infrastructure\models;

use minepark\domain\models\User;
use minepark\domain\models\UserPrivilege;
use minepark\domain\models\UserStatistic;
use pocketmine\player\Player;

class UserStatesMapModel
{
    public User $profile;

    public UserStatistic $statistic;

    public UserPrivilege $privilege;

    public array $permissions;

    public bool $isNew;

    public Player $user;
}