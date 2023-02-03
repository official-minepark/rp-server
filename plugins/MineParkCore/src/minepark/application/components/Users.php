<?php

namespace minepark\application\components;

use minepark\infrastructure\events\UserInitializeEvent;
use minepark\infrastructure\events\UserQuitEvent;
use minepark\infrastructure\services\UserPrivilegesService;
use minepark\infrastructure\services\UsersService;
use minepark\infrastructure\services\UserStatisticsService;
use minepark\infrastructure\stores\UserStatesMapStore;
use pocketmine\command\Command;
use pocketmine\command\utils\CommandStringHelper;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\server\CommandEvent;
use pocketmine\event\server\QueryRegenerateEvent;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\Utils;
use SOFe\AwaitGenerator\Await;

class Users extends BaseComponent
{
    public function __construct(
        private UsersService $usersService,
        private UserStatisticsService $userStatisticsService,
        private UserPrivilegesService $userPrivilegesService,
        private UserStatesMapStore $userStatesMapStore
    )
    {
    }

    public function onPlayerJoin(PlayerJoinEvent $event)
    {
        $event->setJoinMessage("");
        Await::g2c($this->usersService->initializeUser($event->getPlayer()));
    }

    public function onUserInitialize(UserInitializeEvent $event)
    {
        $event->addToQueue($this->userStatisticsService->initializeUser($event->getUser(), $event->getStatesMap()));
        $event->addToQueue($this->userPrivilegesService->initializeUser($event->getUser(), $event->getStatesMap()));
    }

    public function onPlayerQuit(PlayerQuitEvent $event)
    {
        $event->setQuitMessage("");
        Await::g2c($this->usersService->onUserQuit($event->getPlayer()));
    }

    public function onUserQuit(UserQuitEvent $event)
    {
        $event->addToQueue($this->userStatisticsService->onUserQuit($event->getUser(), $event->getStatesMap()));
    }

    public function onCommand(CommandEvent $event)
    {
        $args = CommandStringHelper::parseQuoteAware($event->getCommand());

        $newCommand = array_shift($args);

        foreach ($args as $arg) {
            $statesMap = $this->userStatesMapStore->findByName($arg);

            if ($statesMap !== null) {
                $newCommand = $newCommand . " " . '"' . $statesMap->user->getName() . '"';
            } else {
                $newCommand = $newCommand . " " . '"' . $arg . '"';
            }
        }

        $event->setCommand($newCommand);
    }
}