<?php

namespace minepark\application\components;

use pocketmine\block\BlockIdentifier;
use pocketmine\block\BlockLegacyIds;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\player\PlayerJumpEvent;
use pocketmine\math\Math;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\world\Position;

class AntiCheat extends BaseComponent
{
    private const MAXIMAL_YAW_DIFFERENCE_ATTACK = 140;
    private const MAXIMAL_DISTANCE_ATTACK = 4.5;

    public function onPlayerJump(EntityDamageByEntityEvent $event)
    {
        $entity = $event->getEntity();
        $damager = $event->getDamager();

        if (!$damager instanceof Player) {
            return;
        }

        $entityPosition = $entity->getPosition();
        $damagerPosition = $damager->getPosition();

        $yawBetween = $this->findYawToVector($damagerPosition, $entityPosition);

        $yawDifference = abs($damager->getLocation()->getYaw() - $yawBetween);

        if ($yawDifference > self::MAXIMAL_YAW_DIFFERENCE_ATTACK) {
            $event->cancel();
            return;
        }

        if ($entityPosition->distance($damagerPosition) > self::MAXIMAL_DISTANCE_ATTACK) {
            $event->cancel();
            return;
        }
    }

    private function findYawToVector(Vector3 $source, Vector3 $target): float
    {
        $xDist = $target->getX() - $source->getX();
        $zDist = $target->getZ() - $source->getZ();

        $yaw = atan2($zDist, $xDist) / M_PI * 180 - 90;

        if ($yaw < 0) {
            $yaw += 360.0;
        }

        return $yaw;
    }
}