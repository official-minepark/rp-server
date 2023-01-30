<?php

namespace minepark\application\servercommands\users;

use minepark\application\servercommands\ServerCommand;
use minepark\application\views\UserMenuView;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class MenuServerCommand extends ServerCommand
{
    private const COMMAND_NAME = "menu";

    public function __construct(
        private UserMenuView $userMenuView
    )
    {
        parent::__construct();
    }

    public function getCommandName(): array
    {
        return [
            self::COMMAND_NAME
        ];
    }

    public function getCommandDescription(): string
    {
        return TextFormat::YELLOW . "Открыть меню";
    }

    public function canExecuteConsole(): bool
    {
        return false;
    }

    public function executeCommand(CommandSender $sender, string $commandLabel, array $args): \Generator
    {
        assert($sender instanceof Player);
        yield from $this->userMenuView->sendMainMenu($sender);
    }
}