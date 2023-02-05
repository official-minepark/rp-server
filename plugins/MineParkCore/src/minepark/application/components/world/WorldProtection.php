<?php

namespace minepark\application\components\world;

use minepark\application\components\BaseComponent;
use minepark\infrastructure\models\UserStatesMapModel;
use minepark\infrastructure\services\UsersService;
use minepark\infrastructure\stores\UserStatesMapStore;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\player\Player;

class WorldProtection extends BaseComponent
{
    private const MAP_MODIFY_PERMISSION = "minepark.modifymap";

    public function onBlockBreak(BlockBreakEvent $event)
    {
        if (!$this->canModifyMap($event->getPlayer())) {
            $event->cancel();
        }
    }

    public function onBlockPlace(BlockPlaceEvent $event)
    {
        if (!$this->canModifyMap($event->getPlayer())) {
            $event->cancel();
        }
    }

    public function onInteract(PlayerInteractEvent $event)
    {
        if (!$this->canModifyMap($event->getPlayer())) {
            $event->cancel();
        }
    }

    private function canModifyMap(Player $user): bool
    {
        return $user->hasPermission(self::MAP_MODIFY_PERMISSION);
    }
}