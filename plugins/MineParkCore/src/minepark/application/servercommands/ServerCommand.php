<?php

namespace minepark\application\servercommands;

use minepark\common\di\interfaces\FromContext;
use minepark\common\di\interfaces\Singleton;
use minepark\common\di\traits\SingletonArgsTrait;
use minepark\common\di\traits\SingletonTrait;
use minepark\domain\constants\ServerConstants;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\lang\Translatable;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use SOFe\AwaitGenerator\Await;

abstract class ServerCommand extends Command implements Singleton, FromContext
{
    use SingletonTrait;
    use SingletonArgsTrait;

    private const DEFAULT_PERMISSION = "minepark.command.default";

    public function __construct()
    {
        $names = $this->getCommandName();
        $mainName = array_shift($names);

        parent::__construct($mainName, $this->getCommandDescription(), "/$mainName", $names);
    }

    abstract public function getCommandName(): array;

    abstract public function getCommandDescription(): string;

    public function getCommandPermission(): string
    {
        return self::DEFAULT_PERMISSION;
    }

    public function canExecuteConsole(): bool
    {
        return true;
    }

    abstract public function executeCommand(CommandSender $sender, string $commandLabel, array $args): \Generator;

    public final function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if (!$sender instanceof Player and !$this->canExecuteConsole()) {
            $sender->sendMessage(TextFormat::YELLOW . "Эта команда должна выполняться от лица игрока!");
            return;
        }

        if (!$sender->hasPermission($this->getCommandPermission())) {
            $sender->sendMessage(TextFormat::YELLOW . "У Вас нет прав на использование данной команды.");
            $sender->sendMessage(TextFormat::YELLOW . "Наверное, Вы сможете использовать ее путем приобретения доната на нашем сайте");
            $sender->sendMessage(TextFormat::YELLOW . "Наш сайт: " . TextFormat::AQUA . ServerConstants::SERVER_WEBSITE);
            return;
        }

        Await::g2c($this->executeCommand($sender, $commandLabel, $args));
    }
}