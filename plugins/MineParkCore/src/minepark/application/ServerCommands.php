<?php

namespace minepark\application;

use minepark\application\servercommands as commands;

use minepark\common\di\interfaces\FromContext;
use minepark\common\di\interfaces\Singleton;
use minepark\common\di\traits\SingletonArgsTrait;
use minepark\common\di\traits\SingletonTrait;
use minepark\plugin\MainPlugin;
use pocketmine\command\utils\CommandStringHelper;
use pocketmine\event\server\CommandEvent;
use pocketmine\player\Player;
use pocketmine\Server;

class ServerCommands implements Singleton, FromContext
{
    use SingletonTrait;
    use SingletonArgsTrait;

    public static function fromSingletonArgs(
        Server $server,
        commands\info\VersionServerCommand $version,
        commands\users\MenuServerCommand $menu,
        commands\users\PrivilegesServerCommand $setRank
    ): self
    {
        return new self(
            $server,
            [
                $version,
                $menu,
                $setRank
            ]
        );
    }

    private function __construct(
        private Server $server,
        private array $commands
    )
    {
        $this->initializeCommands();
    }

    private function initializeCommands(): void
    {
        foreach ($this->commands as $command) {
            foreach ($command->getCommandName() as $name) {
                $commandExisting = $this->server->getCommandMap()->getCommand($name);

                if (is_null($commandExisting)) {
                    continue;
                }

                $this->server->getCommandMap()->unregister($commandExisting);
                $commandExisting->unregister($this->server->getCommandMap());
            }

            $this->server->getCommandMap()->register("minepark", $command);
        }
    }
}