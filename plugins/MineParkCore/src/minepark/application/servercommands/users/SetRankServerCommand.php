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

class SetRankServerCommand extends ServerCommand
{
    private const COMMAND_NAME = "setrank";

    public function __construct(
        private UsersDataService $usersDataService,
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
        return "minepark.command.setrank";
    }

    public function executeCommand(CommandSender $sender, string $commandLabel, array $args): \Generator
    {
        if (!isset($args[1])) {
            $sender->sendMessage("Правильное использование: /setrank (имя пользователя) (привилегия)");
            return;
        }

        /**
         * @var ClientResponse<User> $response
         */
        $response = yield from $this->usersDataService->getByName($args[0]);

        if (!$response->isSuccess()) {
            $sender->sendMessage("Игрока " . $args[0] . " не существует");
            return;
        }

        /**
         * @var ClientResponse $response
         */
        $response = yield from $this->usersDataService->setPrivilege($response->getBody()->id, $args[1]);

        if ($response->isSuccess()) {
            $sender->sendMessage("Успех!");
        } else {
            var_dump($response);
        }
    }
}