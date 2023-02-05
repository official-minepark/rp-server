<?php

namespace minepark\application;

use minepark\application\servercommands as commands;

use minepark\common\di\Context;
use minepark\common\di\interfaces\FromContext;
use minepark\common\di\interfaces\Singleton;
use minepark\common\di\traits\SingletonArgsTrait;
use minepark\common\di\traits\SingletonTrait;
use minepark\plugin\MainPlugin;
use pocketmine\command\utils\CommandStringHelper;
use pocketmine\event\server\CommandEvent;
use pocketmine\player\Player;
use pocketmine\Server;
use SOFe\AwaitGenerator\Await;

class ServerCommands implements Singleton, FromContext
{
    use SingletonTrait;
    use SingletonArgsTrait;

    public static function fromSingletonArgs(
        Context $context
    ): self
    {
        return new self(
            $context,
            [
                commands\info\VersionServerCommand::class,
                commands\users\MenuServerCommand::class,
                commands\users\PrivilegesServerCommand::class
            ]
        );
    }

    private function __construct(
        private Context $context,
        private array $commands
    )
    {
        Await::g2c($this->initializeCommands($this->context, $this->commands));
    }

    private function initializeCommands(Context $context, array $commands): \Generator
    {
        $plugin = yield from MainPlugin::getInstance($context);
        $server = $plugin->getServer();

        foreach ($commands as $command) {
            $commandInstance = yield from $command::getInstance($context);

            foreach ($commandInstance->getCommandName() as $name) {
                $commandExisting = $server->getCommandMap()->getCommand($name);

                if (is_null($commandExisting)) {
                    continue;
                }

                $server->getCommandMap()->unregister($commandExisting);
                $commandExisting->unregister($server->getCommandMap());
            }

            $server->getCommandMap()->register("minepark", $commandInstance);
        }
    }
}