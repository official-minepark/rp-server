<?php

namespace minepark\application\servercommands\users;

use minepark\application\servercommands\ServerCommand;
use minepark\common\client\ClientResponse;
use minepark\domain\models\User;
use minepark\infrastructure\dataservices\UsersDataService;
use minepark\infrastructure\services\UsersService;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class PrivilegesServerCommand extends ServerCommand
{
    private const COMMAND_NAME = "privileges";

    private const COMMAND_PERMISSION = "minepark.command.privileges";

    public function __construct(
        private UsersService $usersService,
        private Server $server
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
        return TextFormat::YELLOW . "Установить человеку привилегию";
    }

    public function getCommandPermission(): string
    {
        return self::COMMAND_PERMISSION;
    }

    public function executeCommand(CommandSender $sender, string $commandLabel, array $args): \Generator
    {
        if (!isset($args[0])) {
            $this->sendHelp($sender);
            return;
        }

        if ($args[0] === "set") {
            if (!isset($args[2]) or !is_numeric($args[1])) {
                $sender->sendMessage(TextFormat::YELLOW . "Неправильное использование команды!");
                $this->sendHelp($sender);
                return;
            }

            yield from $this->setSubCommand($sender, $args[1], $args[2]);
        }
    }

    private function setSubCommand(CommandSender $sender, int $userId, string $privilege): \Generator
    {
        /**
         * @var ClientResponse<User> $response
         */
        $response = yield from $this->usersService->changeUserPrivilege($userId, $privilege);

        if ($response) {
            $sender->sendMessage(TextFormat::GREEN . "Привилегия успешно установлена!");
        } else {
            $sender->sendMessage(TextFormat::RED . "Появились ошибки с установкой привилегии");
        }
    }

    private function sendHelp(CommandSender $sender): void
    {
        $sender->sendMessage(TextFormat::YELLOW . "/privileges " . TextFormat::AQUA . "- команда, использующаяся для управления привилегиями");
        $sender->sendMessage(TextFormat::AQUA . "Ее разрешение - " . TextFormat::YELLOW . "minepark.command.privileges" . TextFormat::AQUA . ". В данный момент оно доступно лишь " . TextFormat::YELLOW . "операторам");
        $sender->sendMessage(TextFormat::AQUA . "Чтобы установить привилегию, пропишите " . TextFormat::YELLOW . "/privileges set (id пользователя) (название привилегии)");
    }
}